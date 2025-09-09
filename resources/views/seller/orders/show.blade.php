@extends('layouts.seller.app')

@section('title', 'Order #'.$order->id)

@section('content')
<!-- Toast Notification -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Pass Data to JS -->
<div id="order-data" 
     data-tracking-url="{{ route('tracking.order', $order->id) }}" 
     style="display: none;">
</div>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order Details #{{ $order->id }}</h1>
        <a href="{{ route('seller.orders') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Orders
        </a>
    </div>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge 
                                    @if(in_array($order->status, ['Shipped', 'Delivered'])) bg-success
                                    @elseif($order->status == 'Pending') bg-warning
                                    @elseif($order->status == 'Cancelled') bg-danger
                                    @else bg-secondary @endif">
                                    {{ in_array($order->status, ['Shipped', 'Delivered']) ? 'Completed' : ($order->status == 'Pending' ? 'Incomplete' : $order->status) }}
                                </span>
                            </p>
                            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Product:</strong> {{ optional($order->product)->name ?? 'Deleted Product' }}</p>
                            <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                            <p><strong>Total:</strong> ${{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                    <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Address:</strong> {{ $order->customer_address }}</p>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('seller.orders.update', $order->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Shipped" {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="Delivered" {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Tracking Link -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tracking Link</h5>
                </div>
                <div class="card-body">
                    <p>Share with customer:</p>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               value="{{ route('tracking.order', $order->id) }}" 
                               readonly
                               id="tracking-url-input">
                        <button class="btn btn-outline-secondary" 
                                id="copy-tracking-link">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Copy tracking link
document.addEventListener('DOMContentLoaded', function () {
    const copyBtn = document.getElementById('copy-tracking-link');
    const input = document.getElementById('tracking-url-input');
    const dataEl = document.getElementById('order-data');
    const url = dataEl ? dataEl.dataset.trackingUrl : input.value;

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(url)
                .then(() => alert('Link copied!'))
                .catch(err => console.error('Failed to copy: ', err));
        });
    }
});
</script>
@endpush