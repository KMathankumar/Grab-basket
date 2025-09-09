<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Order #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .tracking-card { border: 1px solid #e9ecef; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>📦 Order Tracking</h1>
            <p class="lead">Track your order status below</p>
        </div>

        <div class="card shadow-sm tracking-card">
            <div class="card-body">
                <h5 class="card-title">Order #{{ $order->id }}</h5>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Product:</strong> {{ $order->product?->name ?? 'Deleted Product' }}</p>
                        <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                        <p><strong>Total:</strong> ${{ number_format($order->total_price, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            <span class="badge 
                                @if(in_array($order->status, ['Shipped', 'Delivered'])) bg-success
                                @elseif($order->status == 'Pending') bg-warning
                                @else bg-danger @endif">
                                {{ $order->status }}
                            </span>
                        </p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-primary">Back to Home</a>
        </div>
    </div>
</body>
</html>