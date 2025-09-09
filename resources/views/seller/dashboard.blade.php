@extends('layouts.seller.app')

@section('title', 'Seller Dashboard')

@section('content')
<!-- Toast Notification -->
@if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1000;">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
@endif

<!-- Main Dashboard -->
<div class="container-fluid p-0"> <!-- ✅ Removed px-4, use p-0 -->
 <div class="px-4 pt-5">
            <h3 class="mb-4">Welcome, {{ auth('seller')->user()->name }}!</h3>

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card-box bg-light p-3 rounded">
                    <h6>Total Sales</h6>
                    <div class="stat-value fw-bold">${{ number_format($totalSales ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-box bg-light p-3 rounded">
                    <h6>Products</h6>
                    <div class="stat-value fw-bold">{{ $totalProducts ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-box bg-light p-3 rounded">
                    <h6>Orders</h6>
                    <div class="stat-value fw-bold">{{ $totalOrders ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card-box bg-light p-3 rounded">
                    <h6>Your Revenue</h6>
                    <div class="stat-value fw-bold">${{ number_format($sellerRevenue ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Filters Row (Full Width) -->
    <div class="px-4"> <!-- ✅ Add padding back for content -->
        <div class="row mb-4">
            <!-- Sales Chart -->
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                <div class="bg-white p-3 rounded shadow-sm h-100">
                    <h5>Sales Overview</h5>
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>

            <!-- Filters -->
            <div class="col-12 col-lg-4">
                <div class="bg-white p-3 rounded shadow-sm h-100">
                    <h5>Filter Orders</h5>
                    <form method="GET" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            <input type="date" name="end_date" class="form-control form-control-sm mt-2" value="{{ request('end_date') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Apply Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Widget (Full Width) -->
    <div class="px-4"> <!-- ✅ Add padding for table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('seller.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->isEmpty())
                    <div class="text-center p-4 text-muted">
                        <p>No recent orders.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ optional($order->product)->name ?? 'Deleted Product' }}</td>
                                        <td>{{ $order->customer_name ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="status-badge 
                                                @if($order->status == 'Delivered') status-delivered
                                                @elseif($order->status == 'Pending') status-pending
                                                @elseif($order->status == 'Shipped') status-shipped
                                                @elseif($order->status == 'Cancelled') status-cancelled
                                                @else text-bg-secondary
                                                @endif">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('seller.orders.show', $order->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="{{ route('tracking.order', $order->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   target="_blank"
                                                   title="Share tracking link">
                                                    📦 Track
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-3 d-flex justify-content-center">
                        {{ $recentOrders->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Pass Data to JavaScript -->
<div id="dashboard-data"
     data-chart-labels='@json($labels ?? [])'
     data-monthly-sales='@json($chartData ?? [])'
     style="display: none;">
</div>

@endsection

@push('scripts')
<script>
// Safely extract Blade data
document.addEventListener('DOMContentLoaded', function () {
    let chartLabels = [];
    let monthlySales = [];

    const dataEl = document.getElementById('dashboard-data');
    if (dataEl) {
        try {
            const labelsStr = dataEl.getAttribute('data-chart-labels');
            const salesStr = dataEl.getAttribute('data-monthly-sales');

            chartLabels = labelsStr ? JSON.parse(labelsStr) : [];
            monthlySales = salesStr ? JSON.parse(salesStr) : [];
        } catch (e) {
            console.warn('Failed to parse chart data, using defaults', e);
            chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            monthlySales = [0, 0, 0, 0, 0, 0];
        }
    }

    // Show toast
    const toastEl = document.querySelector('.toast');
    if (toastEl) {
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    }

    // Render chart
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Sales ($)',
                data: monthlySales,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: context => '$' + Number(context.parsed.y).toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '$' + value.toLocaleString()
                    }
                }
            }
        }
    });
});
</script>
@endpush