@extends('layouts.staff')

@section('title', 'Nhập/xuất kho thủ công')

@section('content')
<div class="page-header">
    <h2>Nhập / xuất kho thủ công</h2>
    <a href="{{ route('staff.inventory-logs.index') }}" class="btn btn-outline-secondary btn-sm">← Nhật ký kho</a>
</div>

<p class="text-muted small">Mọi thao tác được ghi vào <strong>inventory_logs</strong> (nguồn <code>staff_manual</code>) và nhật ký hoạt động.</p>

<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('staff.inventory.adjust.store') }}">
            @csrf
            <div class="form-group">
                <label class="font-weight-bold">Loại mục tiêu</label>
                <select name="target_type" id="target_type" class="form-control" required>
                    <option value="variant" {{ old('target_type', 'variant') === 'variant' ? 'selected' : '' }}>Biến thể (có ID biến thể)</option>
                    <option value="product" {{ old('target_type') === 'product' ? 'selected' : '' }}>Sản phẩm đơn (tồn theo <code>products.quantity</code>)</option>
                </select>
            </div>
            <div class="form-group" id="wrap_variant">
                <label>ID biến thể (<code>product_variants.id</code>)</label>
                <input type="number" name="product_variant_id" class="form-control" value="{{ old('product_variant_id') }}" min="1">
                @error('product_variant_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="form-group" id="wrap_product" style="display:none;">
                <label>ID sản phẩm (<code>products.id</code>)</label>
                <input type="number" name="product_id" class="form-control" value="{{ old('product_id') }}" min="1">
                @error('product_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Loại phiếu</label>
                <select name="type" class="form-control" required>
                    <option value="import" {{ old('type') === 'export' ? '' : 'selected' }}>Nhập kho (+)</option>
                    <option value="export" {{ old('type') === 'export' ? 'selected' : '' }}>Xuất kho (−)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Số lượng</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="note" class="form-control" rows="2" placeholder="Tuỳ chọn">{{ old('note') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ghi nhận</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var sel = document.getElementById('target_type');
    var wv = document.getElementById('wrap_variant');
    var wp = document.getElementById('wrap_product');
    function sync() {
        if (!sel || !wv || !wp) return;
        var isProd = sel.value === 'product';
        wv.style.display = isProd ? 'none' : 'block';
        wp.style.display = isProd ? 'block' : 'none';
    }
    if (sel) { sel.addEventListener('change', sync); sync(); }
})();
</script>
@endpush
@endsection
