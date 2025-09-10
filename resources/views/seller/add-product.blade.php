@extends('layouts.seller.app')

@section('title', 'Add New Product')

@section('content')
<div class="container-fluid px-4 pt-5"> <!-- ✅ Use pt-5 for navbar spacing -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Add New Product</h1>
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

            <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required placeholder="e.g. Wireless Earbuds">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Enter product details...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price ($) *</label>
                            <input type="number" step="0.01" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}" required min="0" placeholder="0.00">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Quantity in Stock *</label>
                            <input type="number" name="stock" id="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock') }}" required min="0" placeholder="e.g. 50">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images (Multiple)</label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Hold Ctrl to select multiple files</small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                        <div class="variant-row d-flex gap-2 mb-2">
                            <input type="text" name="variants[0][size]" class="form-control" placeholder="Size (e.g. M)">
                            <input type="text" name="variants[0][color]" class="form-control" placeholder="Color (e.g. Red)">
                            <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="Price (optional)">
                            <input type="number" name="variants[0][stock]" class="form-control" placeholder="Stock">
                            <button type="button" class="btn btn-danger remove-variant">✕</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-variant">+ Add Variant</button>
                </div>

                <!-- Submit Button -->
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Safely attach event listeners after DOM loads
document.addEventListener('DOMContentLoaded', function () {
    const imagesInput = document.getElementById('images');
    const preview = document.getElementById('image-previews');

    // ✅ Clear and rebuild previews
    function updatePreview() {
        if (!imagesInput || !preview) return;
        preview.innerHTML = '';

        const files = imagesInput.files;
        if (files) {
            Array.from(files).forEach(file => {
                if (!file.type.match('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.height = '100px';
                    img.style.width = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '4px';
                    img.classList.add('border', 'border-light');
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // ✅ Listen for file changes
    if (imagesInput) {
        imagesInput.addEventListener('change', updatePreview);
    }

    // ✅ Add Variant
    let variantIndex = 1;
    const addVariantBtn = document.getElementById('add-variant');
    const container = document.getElementById('variants-container');

    if (addVariantBtn && container) {
        addVariantBtn.addEventListener('click', function () {
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

    // ✅ Remove Variant
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-row').remove();
        }
    });

    // ✅ Run preview on page load (in case of old input)
    updatePreview();
});
</script>
@endpush