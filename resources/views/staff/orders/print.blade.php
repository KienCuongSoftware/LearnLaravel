<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn #{{ $order->id }} — NovaShop</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .muted { color: #666; font-size: 11px; }
        table.lines { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.lines th, table.lines td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.lines th { background: #f5f5f5; }
        .num { text-align: right; white-space: nowrap; }
        .addr { margin-top: 16px; padding: 10px; background: #fafafa; border: 1px solid #e0e0e0; }
        @media print {
            body { margin: 12px; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
    <h1>Phiếu đơn hàng #{{ $order->id }}</h1>
    <p class="muted">NovaShop — in lúc {{ now()->format('d/m/Y H:i') }}</p>

    <div class="addr">
        <strong>Giao đến</strong><br>
        {{ $order->shipping_address ?? '—' }}<br>
        <strong>SĐT:</strong> {{ $order->phone ?? '—' }}<br>
        <strong>Khách:</strong> {{ $order->user->name ?? '—' }} ({{ $order->user->email ?? '—' }})
    </div>

    <table class="lines">
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th class="num">Đơn giá</th>
                <th class="num">SL</th>
                <th class="num">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>
                    {{ $item->product->name ?? '—' }}
                    @if($item->productVariant)
                        <div class="muted">Phân loại: {{ $item->productVariant->display_name }}</div>
                    @endif
                </td>
                <td class="num">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                <td class="num">{{ $item->quantity }}</td>
                <td class="num">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:16px;"><strong>Tổng thanh toán:</strong> {{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
    <p><strong>Trạng thái:</strong> {{ \App\Models\Order::statusLabel($order->status) }}</p>
    <script>window.onload = function() { if (new URLSearchParams(location.search).get('autoprint') === '1') { window.print(); } };</script>
</body>
</html>
