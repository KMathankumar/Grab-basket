<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .tracking-card { border: 1px solid #e9ecef; border-radius: 10px; }
        .status-badge { font-size: 0.85em; padding: 0.5em 0.8em; border-radius: 50px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>📦 Order Tracking</h1>
            <p class="lead">Track your order status below</p>
        </div>

        <div class="card tracking-card shadow-sm">
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
                            <span class="status-badge bg-{{ $order->status_badge }} text-white">
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