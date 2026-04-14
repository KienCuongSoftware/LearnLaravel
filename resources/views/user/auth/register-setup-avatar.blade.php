@extends('layouts.auth')

@section('title', 'Hoàn tất đăng ký - Tạo avatar')

@section('content')
<h2 class="auth-title">Bước 3: Tạo avatar từ tên</h2>
<p class="text-muted small mb-3">Avatar chữ cái sẽ được tạo dựa trên tên bạn vừa nhập.</p>

<div class="text-center mb-3">
    <div style="width: 78px; height: 78px; border-radius: 50%; margin: 0 auto 0.6rem; background: #dc3545; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; font-weight: 700;">
        {{ $initials }}
    </div>
    <div class="small text-muted">Tên hiển thị: <strong>{{ $pendingName }}</strong></div>
</div>

<form method="POST" action="{{ route('register.setup-avatar.submit') }}">
    @csrf
    <button type="submit" class="btn btn-auth-primary">Hoàn tất đăng ký</button>
</form>
@endsection
