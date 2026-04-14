@extends('layouts.auth')

@section('title', 'Khôi phục tài khoản')

@section('content')
<h2 class="auth-title">Khôi phục tài khoản</h2>
<div class="alert alert-warning">
    Tài khoản của bạn đã bị xóa vào <strong>{{ $user->deleted_at?->format('H:i d/m/Y') }}</strong>.
    Bạn có thể khôi phục trong vòng 30 ngày kể từ thời điểm xóa.
</div>

<p class="text-muted small mb-3">Bạn có muốn khôi phục tài khoản không?</p>

<div class="d-flex" style="gap: 0.5rem;">
    <form method="POST" action="{{ route('account.restore.start') }}" class="mb-0 w-100">
        @csrf
        <button type="submit" class="btn btn-auth-primary">Khôi phục</button>
    </form>
    <form method="POST" action="{{ route('account.restore.cancel') }}" class="mb-0 w-100">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100">Hủy</button>
    </form>
</div>

<p class="text-muted small mt-3 mb-0">OTP chỉ được gửi sau khi bạn nhấn "Khôi phục".</p>
@endsection
