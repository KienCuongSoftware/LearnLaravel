<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountDeletionOtpService;
use App\Services\ProfilePasswordOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    private const OTP_VERIFIED_UNTIL_SESSION_KEY = 'staff.profile.password_otp_verified_until';

    private const DELETE_CONFIRMED_AT_SESSION_KEY = 'staff.profile.delete.confirmed_at';

    private const DELETE_CONFIRMED_USER_ID_SESSION_KEY = 'staff.profile.delete.confirmed_user_id';

    public function edit()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        return view('staff.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Kích thước ảnh không được quá 2MB.',
        ]);

        $data = [
            'name' => $request->input('name'),
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('staff.profile.edit')->with('success', 'Đã cập nhật thông tin tài khoản thành công.');
    }

    public function startPasswordOtp(Request $request, ProfilePasswordOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $otpService->send($user);
        $request->session()->forget(self::OTP_VERIFIED_UNTIL_SESSION_KEY);

        return redirect()->route('staff.profile.password.otp.notice')
            ->with('success', 'Đã gửi mã OTP đến email của bạn.');
    }

    public function showPasswordOtpForm()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        return view('staff.profile.password-otp', compact('user'));
    }

    public function verifyPasswordOtp(Request $request, ProfilePasswordOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
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

        $otpService->clear($user);
        $request->session()->put(self::OTP_VERIFIED_UNTIL_SESSION_KEY, now()->addMinutes(15)->toDateTimeString());

        return redirect()->route('staff.profile.password.form')
            ->with('success', 'Xác thực OTP thành công. Bạn có thể đổi mật khẩu.');
    }

    public function resendPasswordOtp(ProfilePasswordOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $otpService->send($user);

        return back()->with('success', 'Đã gửi lại mã OTP mới.');
    }

    public function showPasswordForm(Request $request)
    {
        if (! $this->passwordOtpPassed($request)) {
            return redirect()->route('staff.profile.password.otp.notice')
                ->with('error', 'Vui lòng xác thực OTP trước khi đổi mật khẩu.');
        }

        return view('staff.profile.change-password');
    }

    public function updatePassword(Request $request, ProfilePasswordOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $this->passwordOtpPassed($request)) {
            return redirect()->route('staff.profile.password.otp.notice')
                ->with('error', 'Phiên xác thực OTP đã hết hạn, vui lòng xác thực lại.');
        }

        $validated = $request->validate([
            'old_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'different:old_password'],
        ], [
            'old_password.required' => 'Vui lòng nhập mật khẩu cũ.',
            'old_password.current_password' => 'Mật khẩu cũ không đúng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu cũ.',
        ]);

        $user->update([
            'password' => Hash::make((string) $validated['password']),
        ]);

        $otpService->clear($user);
        $request->session()->forget(self::OTP_VERIFIED_UNTIL_SESSION_KEY);

        return redirect()->route('staff.profile.edit')->with('success', 'Đổi mật khẩu thành công.');
    }

    public function showDeleteConfirm()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $confirmText = $user->email;

        return view('staff.profile.delete-confirm', compact('user', 'confirmText'));
    }

    public function startDeleteOtp(Request $request, AccountDeletionOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $confirmText = $user->email;
        $validated = $request->validate([
            'confirm_text' => ['required', 'string'],
        ], [
            'confirm_text.required' => 'Vui lòng nhập chuỗi xác nhận.',
        ]);

        if (trim((string) $validated['confirm_text']) !== $confirmText) {
            return back()->withErrors([
                'confirm_text' => 'Chuỗi xác nhận chưa chính xác.',
            ])->withInput();
        }

        $otpService->send($user);
        $request->session()->put(self::DELETE_CONFIRMED_AT_SESSION_KEY, now()->toDateTimeString());
        $request->session()->put(self::DELETE_CONFIRMED_USER_ID_SESSION_KEY, $user->id);

        return redirect()->route('staff.profile.delete.otp.notice')
            ->with('success', 'Đã gửi OTP xác nhận xóa tài khoản tới email của bạn.');
    }

    public function showDeleteOtpForm(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $this->deleteFlowAllowed($request, $user)) {
            return redirect()->route('staff.profile.delete.confirm')
                ->with('error', 'Vui lòng xác nhận cảnh báo trước khi nhập OTP.');
        }

        return view('staff.profile.delete-otp', compact('user'));
    }

    public function verifyDeleteOtp(Request $request, AccountDeletionOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $this->deleteFlowAllowed($request, $user)) {
            return redirect()->route('staff.profile.delete.confirm')
                ->with('error', 'Phiên xác nhận xóa tài khoản đã hết hạn.');
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

        try {
            $user->forceFill([
                'email_verification_otp' => null,
                'email_verification_otp_expires_at' => null,
            ])->save();
            $user->delete();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'otp' => 'Không thể xóa tài khoản lúc này do còn dữ liệu ràng buộc.',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')
            ->with('success', 'Tài khoản đã được xóa thành công.');
    }

    public function resendDeleteOtp(Request $request, AccountDeletionOtpService $otpService)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $this->deleteFlowAllowed($request, $user)) {
            return redirect()->route('staff.profile.delete.confirm')
                ->with('error', 'Vui lòng xác nhận lại yêu cầu xóa tài khoản.');
        }

        $otpService->send($user);

        return back()->with('success', 'Đã gửi lại mã OTP xác nhận xóa tài khoản.');
    }

    private function passwordOtpPassed(Request $request): bool
    {
        $until = (string) $request->session()->get(self::OTP_VERIFIED_UNTIL_SESSION_KEY, '');
        if ($until === '') {
            return false;
        }

        $untilTs = strtotime($until);

        return $untilTs !== false && $untilTs > time();
    }

    private function deleteFlowAllowed(Request $request, User $user): bool
    {
        $confirmedAt = (string) $request->session()->get(self::DELETE_CONFIRMED_AT_SESSION_KEY, '');
        $confirmedUserId = (int) $request->session()->get(self::DELETE_CONFIRMED_USER_ID_SESSION_KEY, 0);
        if ($confirmedAt === '' || $confirmedUserId !== (int) $user->id) {
            return false;
        }

        $confirmedTs = strtotime($confirmedAt);

        return $confirmedTs !== false && $confirmedTs > time() - (15 * 60);
    }
}
