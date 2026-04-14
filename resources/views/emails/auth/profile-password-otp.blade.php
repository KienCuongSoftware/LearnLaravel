@extends('emails.layouts.base')

@section('title', 'OTP đổi mật khẩu - NovaShop')
@section('subtitle', 'Xác thực đổi mật khẩu tài khoản')

@section('extra_styles')
        .otp-box {
            margin: 18px 0;
            padding: 16px;
            border: 1px dashed #dc3545;
            background-color: #fff5f5;
            border-radius: 10px;
            text-align: center;
        }
        .otp-label {
            margin: 0 0 8px;
            color: #6c757d;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .otp-code {
            margin: 0;
            font-size: 32px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 6px;
            color: #dc3545;
        }
        .notice {
            margin-top: 16px;
            padding: 12px 14px;
            border-left: 4px solid #17a2b8;
            background-color: #e8f7fb;
            color: #0c5460;
            border-radius: 6px;
            font-size: 14px;
        }
@endsection

@section('content')
<p>Xin chào <strong>{{ $name }}</strong>,</p>
<p>Bạn vừa yêu cầu đổi mật khẩu tài khoản NovaShop. Vui lòng nhập mã OTP bên dưới để tiếp tục:</p>

<div class="otp-box">
    <p class="otp-label">Mã OTP đổi mật khẩu</p>
    <p class="otp-code">{{ $otp }}</p>
</div>

<p>Mã có hiệu lực đến <strong>{{ $expiresAt }}</strong>.</p>

<div class="notice">
    Nếu bạn không yêu cầu đổi mật khẩu, hãy bỏ qua email này và kiểm tra lại bảo mật tài khoản.
</div>
@endsection
