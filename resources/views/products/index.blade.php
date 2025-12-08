@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <!-- Header with title and Add New Product button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📦 Products List</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">➕ Add New Product</a>
    </div>

    <!-- Success message from session flash data -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <!-- SEARCH & SORTING CONTROLS WITH AJAX -->
    <div class="mb-3 row g-3 align-items-end">
        <div class="col-md-5">
            <!-- Real-time search input -->
            <input type="text" id="search" class="form-control" placeholder="Search products...">
        </div>
        <div class="col-md-3">
            <!-- Sorting dropdown -->
            <select id="sort" class="form-select">
                <option value="">Default Sorting</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
            </select>
        </div>
        <div class="col-md-4">
            <!-- Manual filter button -->
            <button id="filter-btn" class="btn btn-outline-primary w-100">🔍 Filter</button>
        </div>
    </div>

    <!-- Main products table card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive" id="product-table-wrapper">
                <!-- Responsive table with advanced features -->
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th width="20%">Details</th>
                            <th>Images</th>
                            <th>Tags</th> <!-- ⭐ NEW COLUMN FOR TAGS -->
                            <th>Size</th>
                            <th>Color</th>
                            <th>Category</th>
                            <th>Price (₹)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Loop through paginated products -->
                        @forelse($products as $product)
                            <!-- IMAGE & TAG PROCESSING -->
                            @php
                                // Safely convert images from DB (array or JSON)
                                $images = $product->images 
                                    ? (is_array($product->images) ? $product->images : json_decode($product->images, true))
                                    : [];

                                // Safely convert tag_ids from DB (array or JSON)
                                $tagIds = $product->tag_ids
                                    ? (is_array($product->tag_ids) ? $product->tag_ids : json_decode($product->tag_ids, true))
                                    : [];

                                // Fetch actual tag names from database
                                $tags = \App\Models\Tag::whereIn('id', $tagIds)->pluck('tag_name');
                            @endphp

                            <tr>
                                <td class="fw-semibold">{{ $product->name }}</td>

                                <!-- Truncated details -->
                                <td style="white-space: normal;">
                                    {{ Str::limit($product->details, 60) }}
                                </td>

                                <!-- MULTIPLE IMAGES DISPLAY (MAX 3 + COUNTER) -->
                                <td>
                                    @if(!empty($images))
                                        <div class="d-flex flex-wrap">
                                            @foreach($images as $index => $img)
                                                @if($index < 3)
                                                    <!-- Show first 3 images -->
                                                    <img src="{{ asset($img) }}" width="60"
                                                         class="rounded shadow-sm border me-1 mb-1">
                                                @endif
                                            @endforeach

                                            @if(count($images) > 3)
                                                <!-- Show counter for additional images -->
                                                <span class="badge bg-secondary">
                                                    +{{ count($images) - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No Images</span>
                                    @endif
                                </td>

                                <!-- TAGS DISPLAY AS BADGES -->
                                <td>
                                    @if(count($tags) > 0)
                                        @foreach($tags as $tag)
                                            <span class="badge bg-info text-dark me-1">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No Tags</span>
                                    @endif
                                </td>

                                <!-- Product attributes -->
                                <td>{{ $product->size }}</td>
                                <td>{{ $product->color }}</td>
                                <td>{{ $product->category }}</td>

                                <!-- Formatted price -->
                                <td class="fw-bold text-success">
                                    ₹{{ number_format($product->price) }}
                                </td>

                                <!-- CRUD Action buttons -->
                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-warning btn-sm me-1">✏ Edit</a>

                                    <!-- Delete form with confirmation -->
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
                            <!-- Empty state -->
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Laravel Pagination Links -->
                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery for AJAX functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function(){
    // AJAX LIVE SEARCH & SORTING FUNCTION
    function fetch_data(page = 1, keyword = '', sort = '') {
        $.ajax({
            url: "{{ route('products.index') }}",
            type: "GET",
            data: { page, keyword, sort },
            success: function(data) {
                // Replace only table content (preserve controls)
                $('#product-table-wrapper').html($(data).find('#product-table-wrapper').html());
            }
        });
    }

    // Real-time search on keyup (debounced by user)
    $('#search').on('keyup', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    // Sort on dropdown change
    $('#sort').on('change', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    // Manual filter button
    $('#filter-btn').on('click', function(){
        fetch_data(1, $('#search').val(), $('#sort').val());
    });

    // Pagination with search/sort preservation
    $(document).on('click', '.pagination a', function(e){
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetch_data(page, $('#search').val(), $('#sort').val());
    });
});
</script>
@endpush
