@extends('layouts.seller.app')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid px-4 pt-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Orders</h1>
        <a href="{{ route('seller.orders.export') }}" class="btn btn-outline-secondary btn-sm">
            📥 Export to CSV
        </a>
    </div>

    <!-- Search & Filters -->
    <form method="GET" class="mb-4">
        <div class="row g-3">
            <!-- Search -->
            <div class="col-md-3">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by Order ID or Customer" 
                       value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div class="col-md-3">
                <select name="status" class="form-control form-select">
                    <option value="">All Statuses</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Incomplete" {{ request('status') == 'Incomplete' ? 'selected' : '' }}>Incomplete</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <!-- End Date -->
            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <!-- Submit & Clear Buttons -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('seller.orders') }}" class="btn btn-sm btn-outline-secondary ms-2 w-100">Clear</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Orders Table -->
    @if($orders->isEmpty())
        <div class="alert alert-info text-center">
            No orders found.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ optional($order->product)->name ?? 'Deleted Product' }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                <span class="badge 
                                    @if(in_array($order->status, ['Shipped', 'Delivered'])) bg-success
                                    @elseif($order->status == 'Pending') bg-warning
                                    @elseif($order->status == 'Cancelled') bg-danger
                                    @else bg-secondary @endif">
                                    {{ in_array($order->status, ['Shipped', 'Delivered']) ? 'Completed' : ($order->status == 'Pending' ? 'Incomplete' : $order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('seller.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-show toast notifications
document.addEventListener('DOMContentLoaded', function () {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    });
});
</script>
@endpush