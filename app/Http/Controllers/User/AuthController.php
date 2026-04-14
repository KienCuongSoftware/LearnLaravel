<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationOtpMail;
use App\Models\User;
use App\Services\AccountRestoreOtpService;
use App\Services\UserInitialsAvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private const ONBOARDING_USER_ID_SESSION_KEY = 'auth.register_onboarding_user_id';

    private const ONBOARDING_NAME_SESSION_KEY = 'auth.register_onboarding_name';

    private const PENDING_NAME_PREFIX = '__PENDING_NAME__';

    private const RESTORE_USER_ID_SESSION_KEY = 'auth.restore.user_id';

    private const RESTORE_WINDOW_DAYS = 30;

    public function showRegistrationForm()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user && $this->isPendingRegistrationProfile($user)) {
            return redirect()->route('register.complete-name');
        }

        return view('user.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name' => self::PENDING_NAME_PREFIX,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar_palette_index' => null,
        ]);

        Auth::login($user);
        $request->session()->put(self::ONBOARDING_USER_ID_SESSION_KEY, $user->id);
        $request->session()->forget(self::ONBOARDING_NAME_SESSION_KEY);

        return redirect()
            ->route('register.complete-name')
            ->with('success', 'Đăng ký thành công. Vui lòng nhập tên để tiếp tục.');
    }

    public function showCompleteNameForm(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->canContinueOnboarding($request, $user)) {
            return $this->redirectByRole($user);
        }

        return view('user.auth.register-complete-name');
    }

    public function completeName(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->canContinueOnboarding($request, $user)) {
            return $this->redirectByRole($user);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
        ]);

        $request->session()->put(self::ONBOARDING_NAME_SESSION_KEY, (string) $validated['name']);

        return redirect()->route('register.setup-avatar')
            ->with('success', 'Đã lưu tên. Tiếp tục tạo avatar từ tên.');
    }

    public function showAvatarSetupForm(Request $request, UserInitialsAvatarService $avatarService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->canContinueOnboarding($request, $user)) {
            return $this->redirectByRole($user);
        }

        $pendingName = trim((string) $request->session()->get(self::ONBOARDING_NAME_SESSION_KEY, ''));
        if ($pendingName === '') {
            return redirect()->route('register.complete-name')
                ->with('error', 'Vui lòng nhập tên trước khi tạo avatar.');
        }

        $initials = $avatarService->initialsFromName($pendingName);

        return view('user.auth.register-setup-avatar', compact('pendingName', 'initials'));
    }

    public function completeAvatar(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->canContinueOnboarding($request, $user)) {
            return $this->redirectByRole($user);
        }

        $pendingName = trim((string) $request->session()->get(self::ONBOARDING_NAME_SESSION_KEY, ''));
        if ($pendingName === '') {
            return redirect()->route('register.complete-name')
                ->with('error', 'Vui lòng nhập tên trước khi tạo avatar.');
        }

        $user->forceFill([
            'name' => $pendingName,
            'avatar' => null,
            'avatar_palette_index' => UserInitialsAvatarService::randomPaletteIndex(),
        ])->save();

        $request->session()->forget([
            self::ONBOARDING_USER_ID_SESSION_KEY,
            self::ONBOARDING_NAME_SESSION_KEY,
        ]);

        $this->sendOtp($user);

        return $this->redirectByRole($user)
            ->with('success', 'Tạo tài khoản thành công. Avatar theo tên đã sẵn sàng, mã OTP đã được gửi về email.');
    }

    public function showLoginForm()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            if ($this->isPendingRegistrationProfile($user)) {
                return redirect()->route('register.complete-name');
            }
            if ($user->is_admin ?? false) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->is_staff ?? false) {
                return redirect()->route('staff.dashboard');
            }

            return redirect()->route('welcome');
        }

        return view('user.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            /** @var User|null $user */
            $user = Auth::user();

            if ($user && ($user->is_blocked ?? false)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tài khoản đã bị khóa.',
                ])->onlyInput('email');
            }

            if ($user && ($user->is_admin ?? false)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'Tài khoản quản trị vui lòng đăng nhập tại trang admin.']);
            }

            if ($user && ($user->is_staff ?? false) && ! ($user->is_admin ?? false)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('staff.login')
                    ->withErrors(['email' => 'Tài khoản nhân viên vui lòng đăng nhập tại trang nhân viên (/staff/login).']);
            }

            if ($user && ! $user->hasVerifiedEmail()) {
                $this->sendOtp($user);

                return redirect()
                    ->route('welcome')
                    ->with('success', 'Đăng nhập thành công. Vui lòng xác thực email để dùng đầy đủ tính năng.');
            }

            if ($user && $this->isPendingRegistrationProfile($user)) {
                $request->session()->put(self::ONBOARDING_USER_ID_SESSION_KEY, $user->id);

                return redirect()->route('register.complete-name')
                    ->with('success', 'Vui lòng hoàn tất hồ sơ đăng ký.');
            }

            return redirect()->intended(route('welcome'))
                ->with('success', 'Đăng nhập thành công.');
        }

        $trashedUser = User::withTrashed()
            ->where('email', (string) $credentials['email'])
            ->first();
        if ($trashedUser && $trashedUser->trashed() && $trashedUser->password && Hash::check((string) $credentials['password'], (string) $trashedUser->password)) {
            if ($this->isDeletedAccountBeyondRestoreWindow($trashedUser)) {
                $this->anonymizeDeletedUser($trashedUser);

                return back()->withErrors([
                    'email' => 'Tài khoản đã bị xóa quá 30 ngày và không thể khôi phục. Vui lòng đăng ký lại.',
                ])->onlyInput('email');
            }

            $request->session()->put(self::RESTORE_USER_ID_SESSION_KEY, $trashedUser->id);

            return redirect()->route('account.restore.notice')
                ->with('error', 'Tài khoản đã bị xóa. Xác nhận để bắt đầu khôi phục.');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    public function showRestorePrompt(Request $request)
    {
        $user = $this->restoreUserFromSession($request);
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Không tìm thấy yêu cầu khôi phục tài khoản.']);
        }

        if ($this->isDeletedAccountBeyondRestoreWindow($user)) {
            $this->anonymizeDeletedUser($user);
            $this->clearRestoreSession($request);

            return redirect()->route('register')
                ->withErrors(['email' => 'Tài khoản đã bị xóa quá 30 ngày và không thể khôi phục.']);
        }

        return view('user.auth.restore-account', compact('user'));
    }

    public function startRestoreOtp(Request $request, AccountRestoreOtpService $otpService)
    {
        $user = $this->restoreUserFromSession($request);
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Yêu cầu khôi phục không còn hiệu lực.']);
        }

        if ($this->isDeletedAccountBeyondRestoreWindow($user)) {
            $this->anonymizeDeletedUser($user);
            $this->clearRestoreSession($request);

            return redirect()->route('register')
                ->withErrors(['email' => 'Tài khoản đã bị xóa quá 30 ngày và không thể khôi phục.']);
        }

        $otpService->send($user);

        return redirect()->route('account.restore.otp.notice')
            ->with('success', 'Đã gửi OTP khôi phục tài khoản về email của bạn.');
    }

    public function cancelRestore(Request $request)
    {
        $this->clearRestoreSession($request);

        return redirect()->route('login')
            ->with('success', 'Đã hủy yêu cầu khôi phục tài khoản.');
    }

    public function showRestoreOtpForm(Request $request)
    {
        $user = $this->restoreUserFromSession($request);
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Yêu cầu khôi phục không còn hiệu lực.']);
        }

        return view('user.auth.restore-account-otp', compact('user'));
    }

    public function verifyRestoreOtp(Request $request, AccountRestoreOtpService $otpService)
    {
        $user = $this->restoreUserFromSession($request);
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Yêu cầu khôi phục không còn hiệu lực.']);
        }

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits' => 'Mã OTP gồm đúng 6 chữ số.',
        ]);

        $error = $otpService->verify($user, (string) $validated['otp']);
        if ($error !== null) {
            return back()->withErrors(['otp' => $error]);
        }

        $user->forceFill([
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ])->save();
        $user->restore();
        $this->clearRestoreSession($request);

        Auth::login($user, true);

        if ($user->is_admin ?? false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('success', 'Đã khôi phục tài khoản. Vui lòng đăng nhập tại trang admin.');
        }

        if ($user->is_staff ?? false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->with('success', 'Đã khôi phục tài khoản. Vui lòng đăng nhập tại trang nhân viên.');
        }

        return redirect()->route('welcome')
            ->with('success', 'Đã khôi phục tài khoản thành công.');
    }

    public function resendRestoreOtp(Request $request, AccountRestoreOtpService $otpService)
    {
        $user = $this->restoreUserFromSession($request);
        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Yêu cầu khôi phục không còn hiệu lực.']);
        }

        $otpService->send($user);

        return back()->with('success', 'Đã gửi lại OTP khôi phục tài khoản.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Đã đăng xuất.');
    }

    public function showVerifyOtpForm(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('welcome');
        }

        return view('user.auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('welcome');
        }

        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits' => 'Mã OTP gồm đúng 6 chữ số.',
        ]);

        if (! $user->email_verification_otp || ! $user->email_verification_otp_expires_at || now()->gt($user->email_verification_otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.',
            ]);
        }

        if (! hash_equals((string) $user->email_verification_otp, (string) $data['otp'])) {
            return back()->withErrors([
                'otp' => 'Mã OTP không đúng.',
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ])->save();

        return redirect()->route('welcome')->with('success', 'Xác thực email thành công.');
    }

    public function resendOtp(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('welcome');
        }

        $this->sendOtp($user);

        return back()->with('success', 'Đã gửi lại mã OTP mới.');
    }

    public function redirectToGoogle()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google OAuth InvalidStateException', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with('error', 'Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.');
        } catch (\Throwable $e) {
            Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('error', 'Không thể đăng nhập với Google.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            if ($user->is_blocked ?? false) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Tài khoản đã bị khóa.',
                ]);
            }
            if (! $user->hasVerifiedEmail()) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
            Auth::login($user, true);

            return redirect()->intended(route('welcome'))->with('success', 'Đăng nhập thành công.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if ($user->is_blocked ?? false) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Tài khoản đã bị khóa.',
                ]);
            }
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'email_verification_otp' => null,
                'email_verification_otp_expires_at' => null,
            ])->save();
            if ($googleUser->getAvatar() && ! $user->avatar) {
                $this->storeGoogleAvatar($user, $googleUser->getAvatar());
            }
            Auth::login($user, true);

            return redirect()->intended(route('welcome'))->with('success', 'Đăng nhập thành công.');
        }

        $user = User::create([
            'name' => $googleUser->getName() ?: $googleUser->getEmail(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'email_verified_at' => now(),
            'password' => null,
        ]);

        if ($googleUser->getAvatar()) {
            $this->storeGoogleAvatar($user, $googleUser->getAvatar());
        }

        Auth::login($user, true);

        return redirect()->intended(route('welcome'))->with('success', 'Đăng ký và đăng nhập thành công.');
    }

    protected function sendOtp(User $user): void
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        $user->forceFill([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => $expiresAt,
        ])->save();

        Mail::to($user->email)->send(new EmailVerificationOtpMail(
            name: $user->name,
            otp: $otp,
            expiresAt: $expiresAt->format('H:i d/m/Y'),
        ));
    }

    protected function storeGoogleAvatar(User $user, string $avatarUrl): void
    {
        try {
            $image = file_get_contents($avatarUrl);
            if ($image === false) {
                return;
            }
            $ext = pathinfo(parse_url($avatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'avatar_'.$user->id.'_'.time().'.'.($ext ?: 'jpg');
            $path = 'avatars/'.$filename;
            Storage::disk('public')->put($path, $image);
            $user->update(['avatar' => $path]);
        } catch (\Throwable $e) {
            // Bỏ qua nếu không tải được avatar
        }
    }

    private function isPendingRegistrationProfile(User $user): bool
    {
        return Str::startsWith((string) $user->name, self::PENDING_NAME_PREFIX);
    }

    private function canContinueOnboarding(Request $request, User $user): bool
    {
        if (! $this->isPendingRegistrationProfile($user)) {
            return false;
        }

        return (int) $request->session()->get(self::ONBOARDING_USER_ID_SESSION_KEY) === (int) $user->id;
    }

    private function redirectByRole(User $user)
    {
        if ($user->is_admin ?? false) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->is_staff ?? false) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('welcome');
    }

    private function restoreUserFromSession(Request $request): ?User
    {
        $userId = (int) $request->session()->get(self::RESTORE_USER_ID_SESSION_KEY, 0);
        if ($userId <= 0) {
            return null;
        }

        $user = User::withTrashed()->find($userId);
        if (! $user || ! $user->trashed()) {
            return null;
        }

        return $user;
    }

    private function clearRestoreSession(Request $request): void
    {
        $request->session()->forget(self::RESTORE_USER_ID_SESSION_KEY);
    }

    private function isDeletedAccountBeyondRestoreWindow(User $user): bool
    {
        if (! $user->deleted_at) {
            return false;
        }

        return $user->deleted_at->lt(now()->subDays(self::RESTORE_WINDOW_DAYS));
    }

    private function anonymizeDeletedUser(User $user): void
    {
        $email = 'deleted-'.$user->id.'-'.Str::lower(Str::random(10)).'@deleted.local';

        $user->forceFill([
            'name' => 'Deleted User',
            'email' => $email,
            'birthday' => null,
            'avatar' => null,
            'google_id' => null,
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
            'password' => Hash::make(Str::random(40)),
        ])->save();
    }
}
