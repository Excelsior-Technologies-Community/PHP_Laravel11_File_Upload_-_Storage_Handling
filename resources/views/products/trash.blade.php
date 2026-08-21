@extends('layouts.admin')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                🗑 Recycle Bin
            </h2>

            <p class="text-muted mb-0">
                Deleted products can be restored or permanently removed.
            </p>

        </div>


        <a
            href="{{ route('products.index') }}"
            class="btn btn-primary">
            ← Back to Products
        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

    <div
        class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

    </div>

    @endif


    {{-- SEARCH --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('products.trash') }}">

                <div class="row g-2">

                    <div class="col-md-8">

                        <input
                            type="text"
                            name="keyword"
                            class="form-control"
                            value="{{ request('keyword') }}"
                            placeholder="Search deleted products...">

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            🔍 Search
                        </button>

                    </div>


                    <div class="col-md-2">

                        <a
                            href="{{ route('products.trash') }}"
                            class="btn btn-outline-secondary w-100">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- RESULTS --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle mb-0">

                    <thead class="table-danger">

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Product
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Color
                            </th>

                            <th>
                                Size
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Deleted At
                            </th>

                            <th class="text-center">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($products as $product)

                        <tr>

                            <td>
                                #{{ $product->id }}
                            </td>


                            <td class="fw-semibold">

                                {{ $product->name }}

                            </td>


                            <td>
                                {{ $product->category }}
                            </td>


                            <td>
                                {{ $product->color }}
                            </td>


                            <td>
                                {{ $product->size }}
                            </td>


                            <td class="fw-bold">

                                ₹{{ number_format(
                                    $product->price
                                ) }}

                            </td>


                            <td>

                                {{ $product->deleted_at
                                    ? $product->deleted_at->format(
                                        'd M Y, h:i A'
                                    )
                                    : '-' }}

                            </td>


                            <td class="text-center">

                                <div
                                    class="d-flex justify-content-center gap-2">

                                    {{-- RESTORE --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'products.restore',
                                            $product->id
                                        ) }}">

                                        @csrf

                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                            onclick="
                                                return confirm(
                                                    'Restore this product?'
                                                )
                                            ">
                                            ♻ Restore
                                        </button>

                                    </form>


                                    {{-- FORCE DELETE --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'products.force-delete',
                                            $product->id
                                        ) }}">

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="
                                                return confirm(
                                                    'WARNING: This will permanently delete the product and its images. Continue?'
                                                )
                                            ">
                                            ❌ Delete Forever
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5">

                                <div class="fs-1">
                                    🗑️
                                </div>

                                <h5>
                                    Recycle Bin is Empty
                                </h5>

                                <p class="text-muted mb-0">
                                    No deleted products found.
                                </p>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="p-3">

                {{ $products->links() }}

            </div>

        </div>

    </div>

</div>

@endsection