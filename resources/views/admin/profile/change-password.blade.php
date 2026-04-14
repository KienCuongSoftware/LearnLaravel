@extends('layouts.admin')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="page-header">
    <h2>Đổi mật khẩu</h2>
    <a class="btn btn-outline-secondary" href="{{ route('admin.profile.edit') }}">Quay lại hồ sơ</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="old_password"><strong>Mật khẩu cũ</strong></label>
                <input type="password" name="old_password" id="old_password" class="form-control" required>
                @error('old_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password"><strong>Mật khẩu mới</strong></label>
                <input type="password" name="password" id="password" class="form-control" required>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation"><strong>Xác nhận mật khẩu mới</strong></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger">Cập nhật mật khẩu</button>
        </form>
    </div>
</div>
@endsection
