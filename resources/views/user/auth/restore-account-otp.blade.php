@extends('layouts.auth')

@section('title', 'OTP khôi phục tài khoản')

@section('content')
<h2 class="auth-title">Xác thực OTP khôi phục tài khoản</h2>
<p class="text-muted">Nhập mã 6 số đã gửi đến email của bạn để hoàn tất khôi phục.</p>

<form method="POST" action="{{ route('account.restore.otp.verify') }}">
    @csrf
    <div class="form-group">
        <label for="otp">Mã OTP</label>
        <input
            type="text"
            name="otp"
            id="otp"
            inputmode="numeric"
            pattern="[0-9]{6}"
            maxlength="6"
            class="form-control"
            value="{{ old('otp') }}"
            placeholder="Nhập 6 chữ số"
            required
            autofocus
        >
    </div>
    <button type="submit" class="btn btn-auth-primary">Khôi phục tài khoản</button>
</form>

<form method="POST" action="{{ route('account.restore.otp.resend') }}" class="mt-2">
    @csrf
    <button type="submit" class="btn btn-link p-0">Gửi lại mã OTP</button>
</form>
@endsection
