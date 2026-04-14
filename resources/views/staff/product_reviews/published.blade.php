@extends('layouts.staff')

@section('title', 'Đánh giá đã duyệt')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h2 class="mb-0">Đánh giá đã duyệt (công khai)</h2>
    <div class="d-flex flex-wrap" style="gap:0.5rem;">
        <a class="btn btn-outline-primary btn-sm" href="{{ route('staff.product-reviews.index') }}">Hàng chờ duyệt</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('staff.dashboard') }}">Dashboard</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="admin-search-form d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
            <input type="text" name="q" class="form-control" style="max-width: 320px;" placeholder="ID, nội dung, SP, người dùng..." value="{{ $q ?? '' }}">
            <button type="submit" class="btn btn-primary">Tìm</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Người dùng</th>
                        <th>Nội dung</th>
                        <th>Trạng thái hiển thị</th>
                        <th>Lịch sử gần nhất</th>
                        <th class="text-right" style="min-width:200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rv)
                    <tr class="{{ $rv->is_hidden ? 'table-secondary' : '' }}">
                        <td>{{ $rv->id }}</td>
                        <td>{{ $rv->product?->name ?? '—' }}</td>
                        <td>{{ $rv->user?->name ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($rv->content, 80) }}</td>
                        <td>
                            @if($rv->is_hidden)
                                <span class="badge badge-secondary">Đã ẩn</span>
                                @if($rv->hidden_reason)<div class="text-muted mt-1">{{ $rv->hidden_reason }}</div>@endif
                            @else
                                <span class="badge badge-success">Đang hiện</span>
                            @endif
                        </td>
                        <td>
                            @php $m = $rv->moderations->first(); @endphp
                            @if($m)
                                <span class="text-muted">{{ $m->created_at->format('d/m H:i') }}</span>
                                <code>{{ $m->action }}</code>
                                {{ $m->user?->name }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            @if(!$rv->is_hidden)
                                <form action="{{ route('staff.product-reviews.hide', $rv) }}" method="POST" class="d-inline-block text-left" style="max-width:220px;">
                                    @csrf
                                    <input type="text" name="hidden_reason" class="form-control form-control-sm mb-1" placeholder="Lý do ẩn (tuỳ chọn)" maxlength="500">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Ẩn khỏi cửa hàng</button>
                                </form>
                            @else
                                <form action="{{ route('staff.product-reviews.unhide', $rv) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Hiện lại</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có đánh giá.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
        <div class="card-footer">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
