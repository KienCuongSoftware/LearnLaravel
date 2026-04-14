@extends('layouts.user')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="page-header">
    <h2>Đổi mật khẩu</h2>
    <a class="btn btn-outline-secondary" href="{{ route('profile') }}">Quay lại hồ sơ</a>
</div>

<div class="card mx-auto" style="max-width: 640px;">
    <div class="card-body">
        <form action="{{ route('profile.password.update') }}" method="POST" class="d-flex flex-column align-items-center">
            @csrf
            @method('PUT')
            <div class="form-group w-100" style="max-width: 420px;">
                <label for="old_password"><strong>Mật khẩu cũ</strong></label>
                <input type="password" name="old_password" id="old_password" class="form-control" required>
                @error('old_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group w-100" style="max-width: 420px;">
                <label for="password"><strong>Mật khẩu mới</strong></label>
                <input type="password" name="password" id="password" class="form-control" required>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group w-100" style="max-width: 420px;">
                <label for="password_confirmation"><strong>Xác nhận mật khẩu mới</strong></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <div class="w-100 text-center" style="max-width: 420px;">
                <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
            </div>
        </form>
    </div>
</div>
@endsection
