@extends('layouts.admin')

@section('title', 'Xóa tài khoản quản trị')

@section('content')
<div class="page-header">
    <h2>Xóa tài khoản</h2>
    <a class="btn btn-outline-secondary" href="{{ route('admin.profile.edit') }}">Quay lại hồ sơ</a>
</div>

<div class="card shadow-sm border-danger">
    <div class="card-body">
        <div class="alert alert-warning mb-3">
            <strong>Cảnh báo:</strong> thao tác này sẽ xóa vĩnh viễn tài khoản của bạn khỏi hệ thống.
        </div>
        <ul class="mb-3">
            <li>Không thể hoàn tác sau khi xác nhận OTP.</li>
            <li>Bạn sẽ bị đăng xuất ngay lập tức khỏi khu vực admin.</li>
            <li>Nếu còn dữ liệu ràng buộc, hệ thống sẽ từ chối xóa.</li>
        </ul>

        <p class="mb-2">Để xác nhận, nhập chính xác chuỗi bên dưới:</p>
        <div class="mb-3"><code>{{ $confirmText }}</code></div>

        <form action="{{ route('admin.profile.delete.start') }}" method="POST">
            @csrf
            <div class="form-group" style="max-width: 420px;">
                <label for="confirm_text"><strong>Chuỗi xác nhận</strong></label>
                <input type="text" name="confirm_text" id="confirm_text" class="form-control" value="{{ old('confirm_text') }}" required autofocus>
                @error('confirm_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-danger">Tiếp tục đến bước OTP</button>
        </form>
    </div>
</div>
@endsection
