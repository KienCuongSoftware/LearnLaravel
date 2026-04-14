@extends('layouts.auth')

@section('title', 'Hoàn tất đăng ký - Nhập tên')

@section('content')
<h2 class="auth-title">Bước 2: Nhập tên hiển thị</h2>
<p class="text-muted small mb-3">Tên sẽ được dùng để tạo avatar chữ cái tự động.</p>

<form method="POST" action="{{ route('register.complete-name.submit') }}">
    @csrf
    <div class="form-group">
        <label for="name">Tên của bạn</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus>
    </div>
    <button type="submit" class="btn btn-auth-primary">Tiếp tục</button>
</form>
@endsection
