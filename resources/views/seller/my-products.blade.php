@extends('layouts.seller.app')

@section('title', 'My Products')

@section('content')
<div class="container-fluid px-4 pt-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>My Products</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('seller.products.create') }}" class="btn btn-success">
                        Add New Product
                    </a>
                    <a href="{{ route('seller.products.export') }}" class="btn btn-outline-secondary btn-sm" title="Export to CSV">
                        📥 Export
                    </a>
                </div>
            </div>

            <!-- Search & Filter -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by product name" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="stock_filter" class="form-control">
                            <option value="">All Stock</option>
                            <option value="in_stock" {{ request('stock_filter') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ request('stock_filter') == 'low_stock' ? 'selected' : '' }}>Low Stock (≤ 5)</option>
                            <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Products Grid -->
            @if($products->isEmpty())
                <div class="alert alert-info text-center">
                    You haven't added any products yet. <a href="{{ route('seller.products.create') }}">Add your first product</a>.
                </div>
            @else
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-12 col-sm-6 col-lg-3"> <!-- ✅ 4 per row on large screens -->
                            <div class="card shadow-sm h-100 
                                @if($product->stock == 0) border-danger 
                                @elseif($product->stock <= 5) border-warning 
                                @else border-light @endif">
                                
                                <!-- Product Image -->
                                @if($product->primaryImage())
                                    <img src="{{ asset('storage/' . $product->primaryImage()->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <span class="text-muted">No Image</span>
                                    </div>
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <!-- Product Name -->
                                    <h5 class="card-title">{{ $product->name }}</h5>

                                    <!-- Category -->
                                    @if($product->category)
                                        <p class="text-muted small mb-1">
                                            <strong>Category:</strong> {{ $product->category->name }}
                                        </p>
                                    @endif

                                    <!-- Description -->
                                    <p class="card-text text-muted mb-2">
                                        {{ Str::limit($product->description, 60) }}
                                    </p>

                                    <!-- Price -->
                                    <p class="mb-1"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>

                                    <!-- Stock with Warning -->
                                    @if($product->stock == 0)
                                        <p class="mb-1 text-danger"><strong>Stock:</strong> Out of Stock</p>
                                    @elseif($product->stock <= 5)
                                        <p class="mb-1 text-warning"><strong>Stock:</strong> Low ({{ $product->stock }})</p>
                                    @else
                                        <p class="mb-1"><strong>Stock:</strong> {{ $product->stock }}</p>
                                    @endif

                                    <!-- Variants Info -->
                                    @if($product->variants->isNotEmpty())
                                        <p class="mb-1 text-primary">
                                            <strong>Variants:</strong> {{ $product->variants->count() }}
                                        </p>
                                        <p class="text-muted small">
                                            @foreach($product->variants->take(2) as $variant)
                                                {{ $variant->size ?? 'One Size' }} / {{ $variant->color ?? 'N/A' }}
                                                @if(!$loop->last) • @endif
                                            @endforeach
                                            @if($product->variants->count() > 2) +{{ $product->variants->count() - 2 }} more @endif
                                        </p>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('seller.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            Edit
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product->id) }}" 
                                              method="POST" 
                                              class="flex-fill"
                                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection