@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <!-- Header with title and Add New Product button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📦 Products List</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">➕ Add New Product</a>
    </div>

    <!-- SEARCH & SORTING CONTROLS WITH AJAX -->
    <div class="mb-3 row g-3 align-items-end">
        <div class="col-md-5">
            <input type="text" id="search" class="form-control" placeholder="Search products...">
        </div>
        <div class="col-md-3">
            <select id="sort" class="form-select">
                <option value="">Default Sorting</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
            </select>
        </div>
        <div class="col-md-4">
            <button id="filter-btn" class="btn btn-outline-primary w-100">🔍 Filter</button>
        </div>
    </div>

    <!-- Main products table card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive" id="product-table-wrapper">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th width="20%">Details</th>
                            <th>Images</th>
                            <th>Tags</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Category</th>
                            <th>Price (₹)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            @php
                                $images = $product->images 
                                    ? (is_array($product->images) ? $product->images : json_decode($product->images, true))
                                    : [];

                                $tagIds = $product->tag_ids
                                    ? (is_array($product->tag_ids) ? $product->tag_ids : json_decode($product->tag_ids, true))
                                    : [];

                                $tags = \App\Models\Tag::whereIn('id', $tagIds)->pluck('tag_name');
                            @endphp

                            <tr>
                                <td class="fw-semibold">{{ $product->name }}</td>

                                <td style="white-space: normal;">
                                    {{ Str::limit($product->details, 60) }}
                                </td>

                                <td>
                                    @if(!empty($images))
                                        <div class="d-flex flex-wrap">
                                            @foreach($images as $index => $img)
                                                @if($index < 3)
                                                    @php
                                                        $thumbPath = 'images/thumbnails/small/' . basename($img);
                                                        $largePath = 'images/thumbnails/large/' . basename($img);
                                                        if (!file_exists(public_path($thumbPath))) {
                                                            $thumbPath = $img;
                                                        }
                                                        if (!file_exists(public_path($largePath))) {
                                                            $largePath = $img;
                                                        }
                                                    @endphp
                                                    <img src="{{ asset($thumbPath) }}" 
                                                         width="60" 
                                                         class="rounded shadow-sm border me-1 mb-1 lightbox-trigger"
                                                         data-large="{{ asset($largePath) }}"
                                                         loading="lazy"
                                                         style="cursor: pointer;">
                                                @endif
                                            @endforeach

                                            @if(count($images) > 3)
                                                <span class="badge bg-secondary">
                                                    +{{ count($images) - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No Images</span>
                                    @endif
                                </td>

                                <td>
                                    @if(count($tags) > 0)
                                        @foreach($tags as $tag)
                                            <span class="badge bg-info text-dark me-1">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No Tags</span>
                                    @endif
                                </td>

                                <td>{{ $product->size }}</td>
                                <td>{{ $product->color }}</td>
                                <td>{{ $product->category }}</td>

                                <td class="fw-bold text-success">
                                    ₹{{ number_format($product->price) }}
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-warning btn-sm me-1">✏ Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this product?')">
                                            🗑 Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function(){
    function fetch_data(page = 1, keyword = '', sort = '') {
        $.ajax({
            url: "{{ route('products.index') }}",
            type: "GET",
            data: { page, keyword, sort },
            success: function(data) {
                $('#product-table-wrapper').html($(data).find('#product-table-wrapper').html());
                initLightbox();
            }
        });
    }

    $('#search').on('keyup', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    $('#sort').on('change', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    $('#filter-btn').on('click', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    $(document).on('click', '.pagination a', function(e){
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetch_data(page, $('#search').val(), $('#sort').val());
    });
    
    initLightbox();
});

function initLightbox() {
    $('.lightbox-trigger').on('click', function() {
        let largeSrc = $(this).data('large');
        $('#lightboxImage').attr('src', largeSrc);
        new bootstrap.Modal(document.getElementById('lightboxModal')).show();
    });
}
</script>
@endpush