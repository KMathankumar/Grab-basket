@extends('layouts.seller.app')

@section('title', 'Edit Product')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
         <br><br><br><br><br>   <h1>Edit Product</h1>
            <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Back to Products
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $product->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price ($) *</label>
                        <input type="number" 
                               step="0.01" 
                               name="price" 
                               id="price" 
                               class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price', $product->price) }}" 
                               required 
                               min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="mb-3">
                        <label for="stock" class="form-label">Quantity in Stock *</label>
                        <input type="number" 
                               name="stock" 
                               id="stock" 
                               class="form-control @error('stock') is-invalid @enderror" 
                               value="{{ old('stock', $product->stock) }}" 
                               required 
                               min="0">
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Current Image -->
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        @if($product->primaryImage())
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->primaryImage()->image) }}" 
                                     alt="Current Image" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px;">
                            </div>
                        @else
                            <p class="text-muted">No image uploaded</p>
                        @endif
                    </div>

                    <!-- New Image Upload -->
                    <div class="mb-3">
                        <label for="images" class="form-label">Upload New Images (Optional)</label>
                        <input type="file" 
                               name="images[]" 
                               id="images" 
                               class="form-control" 
                               multiple 
                               accept="image/*">
                        <small class="text-muted">Hold Ctrl to select multiple files</small>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div class="mb-3">
                        <label class="form-label">Image Previews</label>
                        <div id="image-previews" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>

            <!-- Variants Section -->
            <div class="mb-4">
                <label class="form-label">Variants (Size & Color)</label>
                <div id="variants-container">
                    @if($product->variants->isNotEmpty())
                        @foreach($product->variants as $index => $variant)
                            <div class="variant-row d-flex gap-2 mb-2">
                                <input type="text" 
                                       name="variants[{{ $index }}][size]" 
                                       class="form-control" 
                                       placeholder="Size" 
                                       value="{{ $variant->size }}">
                                <input type="text" 
                                       name="variants[{{ $index }}][color]" 
                                       class="form-control" 
                                       placeholder="Color" 
                                       value="{{ $variant->color }}">
                                <input type="number" 
                                       step="0.01" 
                                       name="variants[{{ $index }}][price]" 
                                       class="form-control" 
                                       placeholder="Price" 
                                       value="{{ $variant->price }}">
                                <input type="number" 
                                       name="variants[{{ $index }}][stock]" 
                                       class="form-control" 
                                       placeholder="Stock" 
                                       value="{{ $variant->stock }}">
                                <button type="button" class="btn btn-danger remove-variant">✕</button>
                            </div>
                        @endforeach
                    @else
                        <div class="variant-row d-flex gap-2 mb-2">
                            <input type="text" name="variants[0][size]" class="form-control" placeholder="Size">
                            <input type="text" name="variants[0][color]" class="form-control" placeholder="Color">
                            <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="Price">
                            <input type="number" name="variants[0][stock]" class="form-control" placeholder="Stock">
                            <button type="button" class="btn btn-danger remove-variant">✕</button>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-variant">+ Add Variant</button>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>
<script>
// Image Preview
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-previews');
    preview.innerHTML = '';

    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function() {
            const img = document.createElement('img');
            img.src = reader.result;
            img.style.height = '100px';
            img.style.width = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '4px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

// Add Variant
/**
 * @type {number}
 */
let variantIndex = 0;

document.addEventListener('DOMContentLoaded', function () {
    // Safely get variant count from PHP
    variantIndex = parseInt("{{ $product->variants->count() ?? 0 }}", 10);

    const addVariantBtn = document.getElementById('add-variant');
    if (addVariantBtn) {
        addVariantBtn.addEventListener('click', function() {
            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.className = 'variant-row d-flex gap-2 mb-2';
            row.innerHTML = `
                <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="Size">
                <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Color">
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="Price">
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="Stock">
                <button type="button" class="btn btn-danger remove-variant">✕</button>
            `;
            container.appendChild(row);
            variantIndex++;
        });
    }
});

// Remove Variant
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant-row').remove();
    }
});
</script>
@endsection