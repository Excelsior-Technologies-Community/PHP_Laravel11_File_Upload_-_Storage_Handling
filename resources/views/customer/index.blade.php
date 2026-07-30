@extends('layouts.customer')

@section('content')

<style>
    .product-card {
        height: 500px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-img {
        height: 300px;
        width: 100%;
        object-fit: cover;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
    }

    .product-details {
        height: 160px;
        overflow: hidden;
    }
</style>

<div class="row">
    @foreach($products as $product)
        @php
            $images = $product->images 
                ? (is_array($product->images) ? $product->images : json_decode($product->images, true))
                : [];
            $firstImage = !empty($images) ? 'images/thumbnails/medium/' . basename($images[0]) : null;
            $largeImage = !empty($images) ? 'images/thumbnails/large/' . basename($images[0]) : null;
            
            if ($firstImage && !file_exists(public_path($firstImage))) {
                $firstImage = $images[0];
            }
            if ($largeImage && !file_exists(public_path($largeImage))) {
                $largeImage = $images[0];
            }
        @endphp
        
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 product-card">

                @if($firstImage)
                    <img src="{{ asset($firstImage) }}" 
                         class="product-img lightbox-trigger" 
                         data-large="{{ asset($largeImage) }}"
                         loading="lazy"
                         alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/300x220" class="product-img" loading="lazy" alt="No image">
                @endif

                <div class="card-body product-details">

                    <h5 class="card-title fw-bold">
                        <strong>Name:</strong> {{ $product->name }}
                    </h5>

                    <p class="text-muted small">
                        <strong>Details:</strong>
                        {{ Str::limit($product->details, 70) }}
                    </p>

                    <ul class="list-unstyled mb-1">
                        <li><strong>Category:</strong> {{ $product->category }}</li>
                        <li><strong>Size:</strong> {{ $product->size }}</li>
                        <li><strong>Color:</strong> {{ $product->color }}</li>
                        <li><strong>Price:</strong> ₹{{ number_format($product->price) }}</li>
                    </ul>

                </div>

            </div>
        </div>
    @endforeach
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-body p-0 text-center">
                <img src="" id="lightboxImage" class="img-fluid" style="max-height: 80vh;">
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.lightbox-trigger').forEach(function(img) {
        img.addEventListener('click', function() {
            let largeSrc = this.getAttribute('data-large');
            document.getElementById('lightboxImage').setAttribute('src', largeSrc);
            new bootstrap.Modal(document.getElementById('lightboxModal')).show();
        });
    });
});
</script>
@endpush