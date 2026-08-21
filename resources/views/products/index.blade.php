@extends('layouts.admin')

@section('content')

<div class="products-page container-fluid py-4">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="products-header mb-4">

        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="products-title-icon">
                    <i class="fa-solid fa-box"></i>
                </div>

                <div>
                    <h2 class="products-title mb-1">
                        Products
                    </h2>

                    <p class="products-subtitle mb-0">
                        Manage products, filters, sorting and exports.
                    </p>
                </div>
            </div>
        </div>

        <div class="products-header-actions">

            <a href="{{ route('products.trash') }}"
                class="product-top-btn product-top-btn-danger">
                <i class="fa-solid fa-trash-can"></i>
                <span>Recycle Bin</span>
            </a>

            <a href="{{ route('products.export', request()->query()) }}"
                class="product-top-btn product-top-btn-success">
                <i class="fa-solid fa-file-csv"></i>
                <span>Export CSV</span>
            </a>

            <a href="{{ route('products.create') }}"
                class="product-top-btn product-top-btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Add Product</span>
            </a>

        </div>

    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))

    <div class="product-alert product-alert-success">

        <div class="d-flex align-items-center gap-3">

            <div class="alert-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div>
                <strong>Success</strong>
                <div>{{ session('success') }}</div>
            </div>

        </div>

        <button type="button"
            class="alert-close"
            data-bs-dismiss="alert">
            <i class="fa-solid fa-xmark"></i>
        </button>

    </div>

    @endif


    {{-- =========================================================
        FILTER CARD
    ========================================================== --}}
    <div class="filter-card">

        <div class="filter-card-header">

            <div class="d-flex align-items-center gap-3">

                <div class="filter-icon">
                    <i class="fa-solid fa-sliders"></i>
                </div>

                <div>
                    <h5 class="filter-title mb-1">
                        Advanced Filters
                    </h5>

                    <p class="filter-subtitle mb-0">
                        Search and filter your products
                    </p>
                </div>

            </div>

            <a href="{{ route('products.index') }}"
                class="reset-filter-btn">
                <i class="fa-solid fa-rotate-left"></i>
                Reset
            </a>

        </div>


        <div class="filter-card-body">

            <form method="GET"
                action="{{ route('products.index') }}"
                id="filterForm">

                <div class="row g-3">

                    {{-- SEARCH --}}
                    <div class="col-xl-4 col-lg-4 col-md-6">

                        <label class="filter-label">
                            Search
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input type="text"
                                name="keyword"
                                class="modern-input input-with-icon"
                                value="{{ request('keyword') }}"
                                placeholder="Search name, details, category...">

                        </div>

                    </div>


                    {{-- CATEGORY --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Category
                        </label>

                        <select name="category"
                            class="modern-select">

                            <option value="">
                                All Categories
                            </option>

                            @foreach($categories as $category)

                            <option value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}>

                                {{ $category }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- COLOR --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Color
                        </label>

                        <select name="color"
                            class="modern-select">

                            <option value="">
                                All Colors
                            </option>

                            @foreach($colors as $color)

                            <option value="{{ $color }}"
                                {{ request('color') == $color ? 'selected' : '' }}>

                                {{ $color }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SIZE --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Size
                        </label>

                        <select name="size"
                            class="modern-select">

                            <option value="">
                                All Sizes
                            </option>

                            @foreach($sizes as $size)

                            <option value="{{ $size }}"
                                {{ request('size') == $size ? 'selected' : '' }}>

                                {{ $size }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- MIN PRICE --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Minimum Price
                        </label>

                        <div class="price-input-wrapper">

                            <span>₹</span>

                            <input type="number"
                                name="min_price"
                                class="modern-input"
                                value="{{ request('min_price') }}"
                                min="0"
                                placeholder="Min">

                        </div>

                    </div>


                    {{-- MAX PRICE --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Maximum Price
                        </label>

                        <div class="price-input-wrapper">

                            <span>₹</span>

                            <input type="number"
                                name="max_price"
                                class="modern-input"
                                value="{{ request('max_price') }}"
                                min="0"
                                placeholder="Max">

                        </div>

                    </div>


                    {{-- SORT --}}
                    <div class="col-xl-3 col-lg-3 col-md-6">

                        <label class="filter-label">
                            Sort By
                        </label>

                        <select name="sort"
                            class="modern-select">

                            <option value="latest"
                                {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>
                                Newest First
                            </option>

                            <option value="oldest"
                                {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>

                            <option value="name-asc"
                                {{ request('sort') == 'name-asc' ? 'selected' : '' }}>
                                Name A → Z
                            </option>

                            <option value="name-desc"
                                {{ request('sort') == 'name-desc' ? 'selected' : '' }}>
                                Name Z → A
                            </option>

                            <option value="price-asc"
                                {{ request('sort') == 'price-asc' ? 'selected' : '' }}>
                                Price Low → High
                            </option>

                            <option value="price-desc"
                                {{ request('sort') == 'price-desc' ? 'selected' : '' }}>
                                Price High → Low
                            </option>

                        </select>

                    </div>


                    {{-- PER PAGE --}}
                    <div class="col-xl-2 col-lg-2 col-md-6">

                        <label class="filter-label">
                            Records Per Page
                        </label>

                        <select name="per_page"
                            class="modern-select">

                            @foreach([5, 10, 25, 50] as $number)

                            <option value="{{ $number }}"
                                {{ $perPage == $number ? 'selected' : '' }}>

                                {{ $number }} Records

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- APPLY --}}
                    <div class="col-xl-3 col-lg-3 col-md-6 d-flex align-items-end">

                        <button type="submit"
                            class="apply-filter-btn w-100">

                            <i class="fa-solid fa-filter"></i>

                            <span>
                                Apply Filters
                            </span>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        RESULTS HEADER
    ========================================================== --}}
    <div class="results-header mb-3">

        <div class="results-count">

            <span class="results-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </span>

            <span>
                Showing

                <strong>
                    {{ $products->firstItem() ?? 0 }}
                </strong>

                to

                <strong>
                    {{ $products->lastItem() ?? 0 }}
                </strong>

                of

                <strong>
                    {{ $products->total() }}
                </strong>

                products
            </span>

        </div>


        @if(
        request('keyword') ||
        request('category') ||
        request('color') ||
        request('size') ||
        request('min_price') ||
        request('max_price')
        )

        <div class="filter-active-badge">
            <i class="fa-solid fa-filter"></i>
            Filters Applied
        </div>

        @endif

    </div>


    {{-- =========================================================
        PRODUCT TABLE
    ========================================================== --}}
    <div class="product-table-card">

        <div class="table-responsive">

            <table class="product-table">

                <thead>

                    <tr>

                        <th>Product</th>

                        <th>Details</th>

                        <th>Images</th>

                        <th>Tags</th>

                        <th>Size</th>

                        <th>Color</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th class="actions-column">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($products as $product)

                    @php

                    /*
                    |--------------------------------------------------------------------------
                    | IMAGES
                    |--------------------------------------------------------------------------
                    */

                    $images = $product->images ?? [];

                    if (!is_array($images)) {

                    $images = json_decode(
                    $images,
                    true
                    ) ?? [];

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TAG IDS
                    |--------------------------------------------------------------------------
                    */

                    $tagIds = $product->tag_ids ?? [];

                    if (!is_array($tagIds)) {

                    $tagIds = json_decode(
                    $tagIds,
                    true
                    ) ?? [];

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | TAGS
                    |--------------------------------------------------------------------------
                    */

                    $tags = \App\Models\Tag::whereIn(
                    'id',
                    $tagIds
                    )->pluck('tag_name');


                    /*
                    |--------------------------------------------------------------------------
                    | PRIMARY IMAGE FIRST
                    |--------------------------------------------------------------------------
                    */

                    if (
                    $product->primary_image &&
                    in_array(
                    $product->primary_image,
                    $images
                    )
                    ) {

                    $images = array_values(
                    array_unique(
                    array_merge(
                    [
                    $product->primary_image
                    ],
                    $images
                    )
                    )
                    );

                    }

                    @endphp


                    <tr>

                        {{-- PRODUCT --}}
                        <td>

                            <div class="product-name-cell">

                                <div class="product-small-icon">
                                    <i class="fa-solid fa-box"></i>
                                </div>

                                <div>

                                    <div class="product-name">
                                        {{ $product->name }}
                                    </div>

                                    <div class="product-id">
                                        #{{ $product->id }}
                                    </div>

                                </div>

                            </div>

                        </td>


                        {{-- DETAILS --}}
                        <td>

                            <div class="details-text">

                                {{ Str::limit(
                                        $product->details,
                                        55
                                    ) }}

                            </div>

                        </td>


                        {{-- IMAGES --}}
                        <td>

                            @if(!empty($images))

                            <div class="product-images">

                                @foreach($images as $index => $img)

                                @if($index < 3)

                                    @php

                                    $thumbPath='images/thumbnails/small/' .
                                    basename($img);

                                    $largePath='images/thumbnails/large/' .
                                    basename($img);


                                    if (
                                    !file_exists(
                                    public_path($thumbPath)
                                    )
                                    ) {

                                    $thumbPath=$img;

                                    }


                                    if (
                                    !file_exists(
                                    public_path($largePath)
                                    )
                                    ) {

                                    $largePath=$img;

                                    }


                                    $isPrimary=$product->primary_image === $img;

                                    @endphp


                                    <div class="product-image-wrapper">

                                        <img
                                            src="{{ asset($thumbPath) }}"
                                            class="product-image"
                                            data-large="{{ asset($largePath) }}"
                                            loading="lazy"
                                            onclick="openLightbox(this)"
                                            alt="{{ $product->name }}">


                                        @if($isPrimary)

                                        <span class="primary-image-badge">
                                            <i class="fa-solid fa-star"></i>
                                        </span>

                                        @endif

                                    </div>

                                    @endif

                                    @endforeach


                                    @if(count($images) > 3)

                                    <span class="more-images">
                                        +{{ count($images) - 3 }}
                                    </span>

                                    @endif

                            </div>

                            @else

                            <span class="no-data">
                                No Images
                            </span>

                            @endif

                        </td>


                        {{-- TAGS --}}
                        <td>

                            @if($tags->count())

                            <div class="tags-container">

                                @foreach($tags as $tag)

                                <span class="product-tag">
                                    {{ $tag }}
                                </span>

                                @endforeach

                            </div>

                            @else

                            <span class="no-data">
                                No Tags
                            </span>

                            @endif

                        </td>


                        {{-- SIZE --}}
                        <td>

                            <span class="data-badge">
                                {{ $product->size ?: '—' }}
                            </span>

                        </td>


                        {{-- COLOR --}}
                        <td>

                            <span class="data-badge">
                                {{ $product->color ?: '—' }}
                            </span>

                        </td>


                        {{-- CATEGORY --}}
                        <td>

                            <span class="category-badge">
                                {{ $product->category ?: '—' }}
                            </span>

                        </td>


                        {{-- PRICE --}}
                        <td>

                            <span class="product-price">
                                ₹{{ number_format($product->price, 2) }}
                            </span>

                        </td>


                        {{-- =====================================================
                                ACTIONS
                            ====================================================== --}}
                        <td class="actions-column">

                            <div class="product-actions">


                                {{-- EDIT --}}
                                <a
                                    href="{{ route('products.edit', $product) }}"
                                    class="product-action-btn product-action-edit"
                                    title="Edit Product">

                                    <i class="fa-solid fa-pen-to-square"></i>

                                    <span>Edit</span>

                                </a>


                                {{-- IMAGE DOWNLOAD --}}
                                @if($product->primary_image)

                                <a
                                    href="{{ route(
                                                'products.image.download',
                                                [
                                                    'filename' => basename(
                                                        $product->primary_image
                                                    )
                                                ]
                                            ) }}"
                                    class="product-action-btn product-action-image"
                                    title="Download Image">

                                    <i class="fa-solid fa-download"></i>

                                    <span>Image</span>

                                </a>

                                @endif


                                {{-- DELETE --}}
                                <form
                                    action="{{ route('products.destroy', $product) }}"
                                    method="POST"
                                    class="product-delete-form">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="product-action-btn product-action-delete"
                                        title="Move to Recycle Bin"
                                        onclick="return confirm('Move this product to Recycle Bin?')">

                                        <i class="fa-solid fa-trash-can"></i>

                                        <span>Delete</span>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                    @empty

                    <tr>

                        <td colspan="9">

                            <div class="empty-products">

                                <div class="empty-icon">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>

                                <h5>
                                    No Products Found
                                </h5>

                                <p>
                                    Try changing your filters or add a new product.
                                </p>

                                <a
                                    href="{{ route('products.create') }}"
                                    class="empty-add-btn">

                                    <i class="fa-solid fa-plus"></i>
                                    Add Product

                                </a>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($products->hasPages())

        <div class="pagination-wrapper">

            {{ $products->appends(request()->query())->links() }}

        </div>

        @endif

    </div>

</div>


{{-- =============================================================
    LIGHTBOX
============================================================= --}}
<div
    class="modal fade"
    id="lightboxModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content lightbox-content">

            <div class="lightbox-header">

                <span>
                    Product Image
                </span>

                <button
                    type="button"
                    class="lightbox-close"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>


            <div class="lightbox-body">

                <img
                    id="lightboxImage"
                    src=""
                    alt="Product Image">

            </div>

        </div>

    </div>

</div>

@endsection


{{-- =============================================================
    STYLES
============================================================= --}}
@push('styles')

<style>
    /* =========================================================
       PAGE
    ========================================================= */

    .products-page {
        background: #f6f8fc;
        min-height: calc(100vh - 80px);
    }


    /* =========================================================
       HEADER
    ========================================================= */

    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .products-title-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, .20);
    }

    .products-title {
        font-size: 27px;
        font-weight: 750;
        color: #111827;
        letter-spacing: -.4px;
    }

    .products-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    .products-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }


    /* =========================================================
       TOP BUTTONS
    ========================================================= */

    .product-top-btn {
        min-height: 42px;
        padding: 0 15px;
        border-radius: 10px;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px;
        font-size: 13px !important;
        font-weight: 650 !important;
        transition: all .2s ease;
        white-space: nowrap;
    }

    .product-top-btn-danger {
        color: #dc2626 !important;
        border: 1px solid #fecaca;
        background: #fff;
    }

    .product-top-btn-success {
        color: #15803d !important;
        border: 1px solid #bbf7d0;
        background: #fff;
    }

    .product-top-btn-primary {
        color: #fff !important;
        border: 1px solid #4f46e5;
        background: #4f46e5;
    }

    .product-top-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
    }


    /* =========================================================
       ALERT
    ========================================================= */

    .product-alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 17px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .product-alert-success {
        background: #ecfdf5;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-icon {
        font-size: 20px;
    }

    .alert-close {
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 17px;
        cursor: pointer;
    }


    /* =========================================================
       FILTER CARD
    ========================================================= */

    .filter-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .04);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .filter-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #eef0f4;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .filter-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }

    .filter-subtitle {
        font-size: 12px;
        color: #9ca3af;
    }

    .reset-filter-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 8px;
        color: #6b7280 !important;
        border: 1px solid #d1d5db;
        background: #fff;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    .reset-filter-btn:hover {
        background: #f9fafb;
        color: #111827 !important;
    }

    .filter-card-body {
        padding: 20px;
    }

    .filter-label {
        display: block;
        margin-bottom: 7px;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }


    /* =========================================================
       FORM INPUTS
    ========================================================= */

    .modern-input,
    .modern-select {
        width: 100% !important;
        height: 42px !important;
        min-height: 42px !important;
        border: 1px solid #dfe3ea !important;
        border-radius: 9px !important;
        background: #fff !important;
        color: #111827 !important;
        font-size: 13px !important;
        padding: 0 12px !important;
        outline: none !important;
        box-shadow: none !important;
        transition: all .2s ease;
    }

    .modern-input:focus,
    .modern-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .10) !important;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper>i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
        z-index: 2;
    }

    .input-with-icon {
        padding-left: 37px !important;
    }

    .price-input-wrapper {
        position: relative;
    }

    .price-input-wrapper>span {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 13px;
        z-index: 2;
    }

    .price-input-wrapper .modern-input {
        padding-left: 30px !important;
    }


    /* =========================================================
       APPLY BUTTON
    ========================================================= */

    .apply-filter-btn {
        height: 42px !important;
        min-height: 42px !important;
        border: 0 !important;
        border-radius: 9px !important;
        background: #4f46e5 !important;
        color: #fff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer;
        box-shadow: 0 5px 14px rgba(79, 70, 229, .18);
    }

    .apply-filter-btn:hover {
        background: #4338ca !important;
        transform: translateY(-1px);
    }


    /* =========================================================
       RESULTS
    ========================================================= */

    .results-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .results-count {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #6b7280;
        font-size: 13px;
    }

    .results-count strong {
        color: #111827;
    }

    .results-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
    }

    .filter-active-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 20px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        font-weight: 700;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .product-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .05);
    }

    .product-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .product-table thead th {
        background: #111827 !important;
        color: #fff !important;
        border: 0 !important;
        padding: 15px 14px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    .product-table tbody td {
        padding: 16px 14px !important;
        border-bottom: 1px solid #edf0f4 !important;
        color: #374151 !important;
        font-size: 13px !important;
        background: #fff !important;
        vertical-align: middle !important;
    }

    .product-table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    .product-table tbody tr:hover td {
        background: #fafbff !important;
    }

    .actions-column {
        min-width: 220px;
        width: 220px;
        text-align: center !important;
    }


    /* =========================================================
       PRODUCT CELL
    ========================================================= */

    .product-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 160px;
    }

    .product-small-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 9px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-name {
        font-weight: 700;
        color: #111827;
        margin-bottom: 2px;
    }

    .product-id {
        color: #9ca3af;
        font-size: 10px;
    }

    .details-text {
        max-width: 220px;
        color: #6b7280;
        line-height: 1.5;
    }


    /* =========================================================
       IMAGES
    ========================================================= */

    .product-images {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 120px;
    }

    .product-image-wrapper {
        position: relative;
        width: 48px;
        height: 48px;
    }

    .product-image {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 9px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: transform .2s ease;
    }

    .product-image:hover {
        transform: scale(1.08);
    }

    .primary-image-badge {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #4f46e5;
        color: #fff;
        font-size: 8px;
    }

    .more-images {
        min-width: 40px;
        height: 40px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }


    /* =========================================================
       TAGS
    ========================================================= */

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        max-width: 160px;
    }

    .product-tag {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 6px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 10px;
        font-weight: 650;
    }

    .data-badge,
    .category-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 11px;
        font-weight: 650;
        white-space: nowrap;
    }

    .category-badge {
        background: #f5f3ff;
        color: #7c3aed;
    }

    .no-data {
        color: #9ca3af;
        font-size: 11px;
    }


    /* =========================================================
       PRICE
    ========================================================= */

    .product-price {
        color: #15803d;
        font-size: 14px;
        font-weight: 750;
        white-space: nowrap;
    }


    /* =========================================================
       ⭐ IMPORTANT ACTION BUTTON FIX
    ========================================================= */

    .product-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        flex-wrap: nowrap !important;
    }

    .product-delete-form {
        display: inline-flex !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .product-action-btn {
        /* IMPORTANT:
           These properties override any global .btn CSS.
        */

        width: auto !important;
        min-width: 68px !important;
        height: 34px !important;
        min-height: 34px !important;

        padding: 0 10px !important;
        margin: 0 !important;

        border-radius: 8px !important;

        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;

        gap: 6px !important;

        font-family: inherit !important;
        font-size: 11px !important;
        line-height: 1 !important;
        font-weight: 700 !important;

        text-decoration: none !important;
        white-space: nowrap !important;

        cursor: pointer !important;

        box-sizing: border-box !important;

        transition:
            background .2s ease,
            color .2s ease,
            border-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease !important;
    }

    .product-action-btn i {
        display: inline-block !important;
        width: auto !important;
        font-size: 11px !important;
        line-height: 1 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .product-action-btn span {
        display: inline !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-size: 11px !important;
        line-height: 1 !important;
    }


    /* EDIT */

    .product-action-edit {
        color: #b45309 !important;
        background: #fffbeb !important;
        border: 1px solid #fcd34d !important;
    }

    .product-action-edit:hover {
        color: #92400e !important;
        background: #fef3c7 !important;
        border-color: #f59e0b !important;
        transform: translateY(-1px);
    }


    /* IMAGE */

    .product-action-image {
        color: #047857 !important;
        background: #ecfdf5 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .product-action-image:hover {
        color: #065f46 !important;
        background: #d1fae5 !important;
        border-color: #34d399 !important;
        transform: translateY(-1px);
    }


    /* DELETE */

    .product-action-delete {
        color: #dc2626 !important;
        background: #fef2f2 !important;
        border: 1px solid #fecaca !important;
    }

    .product-action-delete:hover {
        color: #b91c1c !important;
        background: #fee2e2 !important;
        border-color: #f87171 !important;
        transform: translateY(-1px);
    }


    /* =========================================================
       EMPTY
    ========================================================= */

    .empty-products {
        text-align: center;
        padding: 70px 20px;
    }

    .empty-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 18px;
        background: #f3f4f6;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .empty-products h5 {
        color: #111827;
        font-weight: 700;
    }

    .empty-products p {
        color: #9ca3af;
        font-size: 13px;
    }

    .empty-add-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px;
        padding: 9px 14px;
        border-radius: 8px;
        color: #fff !important;
        background: #4f46e5;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }


    /* =========================================================
       PAGINATION
    ========================================================= */

    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid #edf0f4;
        display: flex;
        justify-content: flex-end;
    }

    .pagination-wrapper .pagination {
        margin: 0 !important;
    }


    /* =========================================================
       LIGHTBOX
    ========================================================= */

    .lightbox-content {
        background: #111827 !important;
        border: 0 !important;
        border-radius: 14px !important;
        overflow: hidden;
    }

    .lightbox-header {
        padding: 14px 18px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 650;
        font-size: 13px;
    }

    .lightbox-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 8px;
        background: rgba(255, 255, 255, .1);
        color: #fff;
        cursor: pointer;
    }

    .lightbox-body {
        padding: 10px;
        text-align: center;
        background: #030712;
    }

    .lightbox-body img {
        max-width: 100%;
        max-height: 75vh;
        border-radius: 8px;
        object-fit: contain;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {

        .products-title {
            font-size: 22px;
        }

        .products-header-actions {
            width: 100%;
        }

        .product-top-btn {
            flex: 1;
        }

        .filter-card-header {
            align-items: flex-start;
        }

        .results-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .pagination-wrapper {
            justify-content: center;
        }

    }
</style>

@endpush


{{-- =============================================================
    SCRIPTS
============================================================= --}}
@push('scripts')

<script>
    function openLightbox(element) {

        const image =
            element.getAttribute('data-large');

        const lightboxImage =
            document.getElementById('lightboxImage');

        lightboxImage.src = image;

        const modalElement =
            document.getElementById('lightboxModal');

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();
    }
</script>

@endpush