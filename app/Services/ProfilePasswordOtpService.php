<?php

namespace App\Services;

use App\Mail\ProfilePasswordOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ProfilePasswordOtpService
{
    public const TTL_MINUTES = 10;

    public function send(User $user): void
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::TTL_MINUTES);

        $user->forceFill([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => $expiresAt,
        ])->save();

        Mail::to($user->email)->send(new ProfilePasswordOtpMail(
            name: $user->name,
            otp: $otp,
            expiresAt: $expiresAt->format('H:i d/m/Y'),
        ));
    }

    public function verify(User $user, string $otp): ?string
    {
        if (! $user->email_verification_otp || ! $user->email_verification_otp_expires_at) {
            return 'Mã OTP không tồn tại. Vui lòng yêu cầu gửi mã mới.';
        }

        if (now()->gt($user->email_verification_otp_expires_at)) {
            return 'Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.';
        }

        if (! hash_equals((string) $user->email_verification_otp, (string) $otp)) {
            return 'Mã OTP không đúng.';
        }

        return null;
    }

    public function clear(User $user): void
    {
        $user->forceFill([
            'email_verification_otp' => null,
            'email_verification_otp_expires_at' => null,
        ])->save();
    }
}
