@extends('layouts.user')

@section('title', 'Xác thực OTP đổi mật khẩu')

@section('content')
<div class="page-header">
    <h2>Xác thực OTP đổi mật khẩu</h2>
    <a class="btn btn-outline-secondary" href="{{ route('profile') }}">Quay lại hồ sơ</a>
</div>

<div class="card mx-auto" style="max-width: 760px;">
    <div class="card-body">
        <p class="text-muted">Nhập mã OTP 6 số đã gửi đến email <strong>{{ $user->email }}</strong>.</p>
        <form method="POST" action="{{ route('profile.password.otp.verify') }}">
            @csrf
            <div class="form-group">
                <label for="otp"><strong>Mã OTP</strong></label>
                <input type="text" name="otp" id="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="form-control" style="max-width: 360px;" value="{{ old('otp') }}" placeholder="Nhập 6 chữ số" required autofocus>
                @error('otp')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Xác thực OTP</button>
        </form>

        <form method="POST" action="{{ route('profile.password.otp.resend') }}" class="mt-3 mb-0">
            @csrf
            <button type="submit" class="btn btn-link p-0">Gửi lại mã OTP</button>
        </form>
    </div>
</div>
@endsection
