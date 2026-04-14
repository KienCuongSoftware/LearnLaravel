@extends('layouts.staff')

@section('title', 'Trang làm việc')

@section('content')
<div class="page-header">
    <h2>Trang làm việc</h2>
</div>

<p class="text-muted mb-4">Chào <strong>{{ auth()->user()->name }}</strong>. Chọn nhanh tác vụ thường dùng:</p>

<div class="row mb-3">
    <div class="col-sm-6 col-lg-4 mb-2">
        <div class="card shadow-sm h-100 border-left-danger" style="border-left-width: 4px;">
            <div class="card-body py-3">
                <div class="small text-muted">Đơn chờ xử lý</div>
                <div class="h4 mb-0">{{ number_format($pendingOrdersCount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-2">
        <div class="card shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted">Đánh giá chờ &gt; 24h</div>
                <div class="h4 mb-0 {{ $reviewsPendingOver24h > 0 ? 'text-danger' : '' }}">{{ number_format($reviewsPendingOver24h) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-2">
        <div class="card shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted">Đơn tạo hôm nay</div>
                <div class="h4 mb-0">{{ number_format($ordersToday) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->user()->staffCanOrders())
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Đơn hàng</h5>
                <p class="card-text text-muted small mb-2">Hôm nay: <strong>{{ $ordersToday }}</strong> đơn mới.</p>
                <a href="{{ route('staff.orders.index') }}" class="btn btn-primary btn-sm">Mở danh sách</a>
            </div>
        </div>
    </div>
    @endif
    @if(auth()->user()->staffCanReviews())
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Duyệt đánh giá</h5>
                <p class="card-text text-muted small mb-2">Chờ duyệt: <strong>{{ $pendingReviews }}</strong>.</p>
                <a href="{{ route('staff.product-reviews.index') }}" class="btn btn-primary btn-sm">Mở hàng chờ</a>
            </div>
        </div>
    </div>
    @endif
    @if(auth()->user()->staffCanInventory())
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Nhập / xuất kho</h5>
                <p class="card-text text-muted small mb-2">Nhật ký & điều chỉnh thủ công.</p>
                <a href="{{ route('staff.inventory-logs.index') }}" class="btn btn-primary btn-sm">Mở nhật ký</a>
                <a href="{{ route('staff.inventory.adjust') }}" class="btn btn-outline-primary btn-sm mt-1">Nhập/xuất tay</a>
            </div>
        </div>
    </div>
    @endif
</div>

<p class="small text-muted mb-2"><a href="{{ route('staff.activity-log.index') }}">Nhật ký hoạt động nhân viên →</a></p>

<div class="row mt-2">
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white font-weight-bold border-bottom">Đơn tạo 7 ngày gần nhất</div>
            <div class="card-body" style="min-height: 280px;">
                <canvas id="staffChartOrders7d" aria-label="Biểu đồ đơn theo ngày"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white font-weight-bold border-bottom">Đơn theo trạng thái</div>
            <div class="card-body" style="min-height: 280px;">
                <canvas id="staffChartOrdersByStatus" aria-label="Biểu đồ tròn trạng thái đơn"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var font = { family: "system-ui, -apple-system, 'Segoe UI', sans-serif" };
    var tickColor = '#6b7280';
    var gridColor = 'rgba(28, 25, 23, 0.06)';
    var doughnutPalette = ['#b71c1c', '#c62828', '#d32f2f', '#e53935', '#c2410c', '#b45309', '#6b7280', '#9ca3af'];

    var barEl = document.getElementById('staffChartOrders7d');
    if (barEl) {
        var grad = barEl.getContext('2d').createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(183, 28, 28, 0.85)');
        grad.addColorStop(1, 'rgba(183, 28, 28, 0.35)');
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: @json($chartLast7Labels),
                datasets: [{
                    label: 'Số đơn',
                    data: @json($chartLast7Counts),
                    backgroundColor: grad,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: font, color: tickColor, maxRotation: 0 }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: font, color: tickColor },
                        grid: { color: gridColor }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    var doughnutEl = document.getElementById('staffChartOrdersByStatus');
    if (doughnutEl) {
        var dataArr = @json($chartStatusData);
        var bg = dataArr.map(function(_, i) { return doughnutPalette[i % doughnutPalette.length]; });
        new Chart(doughnutEl, {
            type: 'doughnut',
            data: {
                labels: @json($chartStatusLabels),
                datasets: [{
                    data: dataArr,
                    backgroundColor: bg,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: font,
                            color: tickColor,
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
