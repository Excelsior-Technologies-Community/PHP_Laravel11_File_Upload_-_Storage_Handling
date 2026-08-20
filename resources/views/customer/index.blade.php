@extends('layouts.customer')

@section('content')

<style>

.product-card {

    height: 540px;

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

    height: 170px;

    overflow: hidden;

}

.primary-badge {

    position: absolute;

    top: 10px;

    left: 10px;

    z-index: 2;

}

.image-wrapper {

    position: relative;

}

</style>


<div class="row">

@forelse($products as $product)

    @php

        $images =
            $product->images
                ? (
                    is_array($product->images)
                    ? $product->images
                    : json_decode(
                        $product->images,
                        true
                    )
                )
                : [];


        /*
        |--------------------------------------------------------------------------
        | Use Primary Image
        |--------------------------------------------------------------------------
        */

        $displayImage =
            $product->primary_image;

        if (
            !$displayImage ||
            !in_array(
                $displayImage,
                $images
            )
        ) {

            $displayImage =
                !empty($images)
                    ? $images[0]
                    : null;

        }


        $mediumImage = null;
        $largeImage = null;


        if ($displayImage) {

            $mediumImage =
                'images/thumbnails/medium/' .
                basename($displayImage);

            $largeImage =
                'images/thumbnails/large/' .
                basename($displayImage);


            if (
                !file_exists(
                    public_path($mediumImage)
                )
            ) {

                $mediumImage =
                    $displayImage;

            }


            if (
                !file_exists(
                    public_path($largeImage)
                )
            ) {

                $largeImage =
                    $displayImage;

            }

        }

    @endphp


    <div class="col-md-3 mb-4">

        <div
            class="card shadow-sm border-0 product-card"
        >


            <!-- PRODUCT IMAGE -->

            @if($mediumImage)

                <div class="image-wrapper">

                    <span
                        class="badge bg-primary primary-badge"
                    >
                        ⭐ Primary Image
                    </span>


                    <img
                        src="{{ asset($mediumImage) }}"
                        class="product-img lightbox-trigger"
                        data-large="{{ asset($largeImage) }}"
                        loading="lazy"
                        alt="{{ $product->name }}"
                    >

                </div>

            @else

                <img
                    src="https://via.placeholder.com/300x220"
                    class="product-img"
                    loading="lazy"
                    alt="No image"
                >

            @endif


            <!-- DETAILS -->

            <div class="card-body product-details">

                <h5 class="card-title fw-bold">

                    <strong>
                        Name:
                    </strong>

                    {{ $product->name }}

                </h5>


                <p class="text-muted small">

                    <strong>
                        Details:
                    </strong>

                    {{ Str::limit(
                        $product->details,
                        70
                    ) }}

                </p>


                <ul class="list-unstyled mb-1">

                    <li>
                        <strong>
                            Category:
                        </strong>

                        {{ $product->category }}

                    </li>


                    <li>
                        <strong>
                            Size:
                        </strong>

                        {{ $product->size }}

                    </li>


                    <li>
                        <strong>
                            Color:
                        </strong>

                        {{ $product->color }}

                    </li>


                    <li>
                        <strong>
                            Price:
                        </strong>

                        ₹{{ number_format(
                            $product->price
                        ) }}

                    </li>

                </ul>

            </div>


            <!-- DOWNLOAD -->

            @if($displayImage)

                <div class="card-footer bg-white border-0">

                    <a
                        href="{{ route(
                            'products.image.download',
                            [
                                'filename' =>
                                    basename(
                                        $displayImage
                                    )
                            ]
                        ) }}"
                        class="btn btn-success btn-sm w-100"
                    >
                        ⬇ Download Primary Image
                    </a>

                </div>

            @endif


        </div>

    </div>

@empty

    <div class="col-12">

        <div class="alert alert-info text-center">

            No products available.

        </div>

    </div>

@endforelse

</div>


<!-- LIGHTBOX -->

<div
    class="modal fade"
    id="lightboxModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-lg modal-dialog-centered"
    >

        <div
            class="modal-content bg-dark border-0"
        >

            <div
                class="modal-body p-0 text-center"
            >

                <img
                    src=""
                    id="lightboxImage"
                    class="img-fluid"
                    style="max-height:80vh;"
                >

            </div>


            <div
                class="modal-footer justify-content-center border-0"
            >

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        document
            .querySelectorAll(
                '.lightbox-trigger'
            )
            .forEach(
                function (img) {

                    img.addEventListener(
                        'click',
                        function () {

                            const largeSrc =
                                this.getAttribute(
                                    'data-large'
                                );


                            document
                                .getElementById(
                                    'lightboxImage'
                                )
                                .setAttribute(
                                    'src',
                                    largeSrc
                                );


                            new bootstrap.Modal(
                                document.getElementById(
                                    'lightboxModal'
                                )
                            ).show();

                        }
                    );

                }
            );

    }
);

</script>

@endpush