@extends('layouts.staff')

@section('title', 'Nhật ký hoạt động')

@section('content')
<div class="page-header">
    <h2>Nhật ký hoạt động nhân viên</h2>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="admin-search-form d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
            <input type="text" name="action" class="form-control" style="max-width: 280px;" placeholder="Lọc theo action (vd: order.status)..." value="{{ $action ?? '' }}">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="thead-light">
                    <tr>
                        <th style="width:140px;">Thời gian</th>
                        <th style="width:160px;">Người thực hiện</th>
                        <th style="width:200px;">Action</th>
                        <th>Đối tượng</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                    <tr>
                        <td class="text-muted">{{ $act->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $act->user?->name ?? '—' }}</td>
                        <td><code>{{ $act->action }}</code></td>
                        <td>
                            @if($act->subject_type && $act->subject_id)
                                {{ class_basename($act->subject_type) }} #{{ $act->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-muted">{{ $act->properties ? json_encode($act->properties, JSON_UNESCAPED_UNICODE) : '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Chưa có bản ghi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($activities->hasPages())
        <div class="card-footer">{{ $activities->links() }}</div>
    @endif
</div>
@endsection
