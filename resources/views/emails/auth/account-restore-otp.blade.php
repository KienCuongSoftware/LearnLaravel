@extends('emails.layouts.base')

@section('title', 'OTP khôi phục tài khoản - NovaShop')
@section('subtitle', 'Xác thực khôi phục tài khoản')

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
@endsection

@section('content')
<p>Xin chào <strong>{{ $name }}</strong>,</p>
<p>Bạn vừa yêu cầu khôi phục tài khoản NovaShop. Nhập OTP bên dưới để xác nhận:</p>

<div class="otp-box">
    <p class="otp-label">Mã OTP khôi phục tài khoản</p>
    <p class="otp-code">{{ $otp }}</p>
</div>

<p>Mã có hiệu lực đến <strong>{{ $expiresAt }}</strong>.</p>
@endsection
