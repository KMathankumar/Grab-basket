<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #{{ $order->id }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .tracking-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
        }
        .status-badge {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
            border-radius: 50px;
        }
        /* Timeline */
        .timeline {
            position: relative;
            margin: 30px 0;
            padding-left: 20px;
            border-left: 2px solid #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            left: -31px;
            top: 5px;
            background-color: #28a745;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #28a745;
        }
        .timeline-item.pending::before {
            background-color: #ffc107;
            box-shadow: 0 0 0 2px #ffc107;
        }
        .timeline-item.shipped::before,
        .timeline-item.delivered::before {
            background-color: #28a745;
            box-shadow: 0 0 0 2px #28a745;
        }
        .timeline-item.cancelled::before {
            background-color: #dc3545;
            box-shadow: 0 0 0 2px #dc3545;
        }
        .timeline-date {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1><i class="bi bi-box"></i> Order Tracking</h1>
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
                            <span class="status-badge 
                                @if(in_array($order->status, ['Shipped', 'Delivered'])) bg-success text-white
                                @elseif($order->status == 'Pending') bg-warning text-dark
                                @elseif($order->status == 'Cancelled') bg-danger text-white
                                @else bg-secondary text-white @endif">
                                {{ $order->status }}
                            </span>
                        </p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Tracking Timeline -->
                <div class="mt-4">
                    <h6>Order Progress</h6>
                    <div class="timeline">
                        <!-- Order Placed -->
                        <div class="timeline-item">
                            <strong>Order Placed</strong>
                            <div class="timeline-date">{{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('g:i A') }}</div>
                        </div>

                        <!-- Order Shipped -->
                        @if(in_array($order->status, ['Shipped', 'Delivered']))
                            <div class="timeline-item shipped">
                                <strong>Order Shipped</strong>
                                <div class="timeline-date">{{ $order->updated_at->format('M d, Y') }} at {{ $order->updated_at->format('g:i A') }}</div>
                            </div>
                        @else
                            <div class="timeline-item pending">
                                <strong>Order Shipped</strong>
                                <div class="timeline-date text-muted">Expected soon</div>
                            </div>
                        @endif

                        <!-- Order Delivered -->
                        @if($order->status == 'Delivered')
                            <div class="timeline-item delivered">
                                <strong>Order Delivered</strong>
                                <div class="timeline-date">{{ $order->updated_at->format('M d, Y') }} at {{ $order->updated_at->format('g:i A') }}</div>
                            </div>
                        @elseif($order->status == 'Cancelled')
                            <div class="timeline-item cancelled">
                                <strong>Order Cancelled</strong>
                                <div class="timeline-date">{{ $order->updated_at->format('M d, Y') }} at {{ $order->updated_at->format('g:i A') }}</div>
                            </div>
                        @else
                            <div class="timeline-item pending">
                                <strong>Order Delivered</strong>
                                <div class="timeline-date text-muted">Coming soon</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/seller/dashboard" class="btn btn-outline-primary">Back to Home</a>
        </div>
    </div>
</body>
</html>