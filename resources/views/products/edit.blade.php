@extends('layouts.admin')

@section('content')

<div class="container">

    <h1 class="mb-4">
        Edit Product
    </h1>


    <form
        id="editProductForm"
        action="{{ route('products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        @method('PUT')


        {{-- NAME --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Name
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ old('name', $product->name) }}"
                required
            >

        </div>


        {{-- DETAILS --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Details
            </label>

            <textarea
                name="details"
                class="form-control"
                required
            >{{ old('details', $product->details) }}</textarea>

        </div>


        {{-- TAGS --}}

        @php

            $selectedTags = $product->tag_ids
                ? (
                    is_array($product->tag_ids)
                        ? $product->tag_ids
                        : json_decode(
                            $product->tag_ids,
                            true
                        )
                )
                : [];

        @endphp


        <div class="mb-3">

            <label class="form-label fw-bold">
                Select Tags
            </label>

            <select
                name="tag_ids[]"
                id="tagSelect"
                class="form-select"
                multiple
            >

                @foreach($tags as $tag)

                    <option
                        value="{{ $tag->id }}"
                        {{ in_array(
                            $tag->id,
                            $selectedTags
                        ) ? 'selected' : '' }}
                    >
                        {{ $tag->tag_name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- EXISTING IMAGES --}}

        @php

            $existingImages = $product->images
                ? (
                    is_array($product->images)
                        ? $product->images
                        : json_decode(
                            $product->images,
                            true
                        )
                )
                : [];

        @endphp


        <div class="mb-4">

            <label class="form-label fw-bold">
                Existing Images
            </label>


            @if(!empty($existingImages))

                <div class="d-flex flex-wrap">

                    @foreach($existingImages as $img)

                        @php

                            $thumbPath =
                                'images/thumbnails/small/' .
                                basename($img);

                            if (
                                !file_exists(
                                    public_path($thumbPath)
                                )
                            ) {

                                $thumbPath = $img;

                            }

                            $isPrimary =
                                $product->primary_image === $img;

                        @endphp


                        <div
                            class="existing-image-card
                            {{ $isPrimary
                                ? 'primary-image-card'
                                : '' }}"
                        >

                            <div class="position-relative">

                                <img
                                    src="{{ asset($thumbPath) }}"
                                    class="existing-image"
                                    alt="{{ $product->name }}"
                                >


                                @if($isPrimary)

                                    <span class="primary-label">
                                        ⭐ PRIMARY
                                    </span>

                                @endif

                            </div>


                            <div class="image-actions">

                                {{-- PRIMARY --}}

                                <label
                                    class="
                                    btn
                                    btn-sm
                                    {{ $isPrimary
                                        ? 'btn-primary'
                                        : 'btn-outline-primary' }}
                                    "
                                >

                                    <input
                                        type="radio"
                                        name="primary_image"
                                        value="{{ $img }}"
                                        class="d-none"
                                        {{ $isPrimary
                                            ? 'checked'
                                            : '' }}
                                    >

                                    ⭐ Primary

                                </label>


                                {{-- DOWNLOAD --}}

                                <a
                                    href="{{ route(
                                        'products.image.download',
                                        [
                                            'filename' =>
                                                basename($img)
                                        ]
                                    ) }}"
                                    class="btn btn-sm btn-success"
                                >
                                    ⬇ Download
                                </a>


                                {{-- DELETE --}}

                                <label
                                    class="
                                    btn
                                    btn-sm
                                    btn-outline-danger
                                    "
                                >

                                    <input
                                        type="checkbox"
                                        name="delete_images[]"
                                        value="{{ $img }}"
                                        class="
                                        delete-image-checkbox
                                        d-none
                                        "
                                    >

                                    🗑 Delete

                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <p class="text-muted">
                    No Images Found
                </p>

            @endif

        </div>


        {{-- ADD NEW IMAGES --}}

        <div class="mb-4">

            <label class="form-label fw-bold">
                Add New Images
            </label>


            <div
                class="
                border
                rounded
                p-4
                text-center
                upload-box
                "
                onclick="
                    document
                    .getElementById('productImages')
                    .click()
                "
            >

                <div class="py-3">

                    <div style="font-size:40px;">
                        📤
                    </div>

                    <p class="mb-1 fw-semibold">
                        Click to upload more images
                    </p>

                    <small class="text-muted">
                        JPG, PNG, GIF up to 2MB per image
                    </small>

                </div>

            </div>


            <input
                type="file"
                name="images[]"
                id="productImages"
                class="d-none"
                multiple
                accept="image/*"
            >


            {{-- New image primary index --}}

            <input
                type="hidden"
                name="primary_image_index"
                id="primaryImageIndex"
                value=""
            >


            <div
                id="imagePreviewContainer"
                class="d-flex flex-wrap mt-3"
            ></div>

        </div>


        {{-- SIZE --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Size
            </label>

            <input
                type="text"
                name="size"
                class="form-control"
                value="{{ old('size', $product->size) }}"
                required
            >

        </div>


        {{-- COLOR --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Color
            </label>

            <input
                type="text"
                name="color"
                class="form-control"
                value="{{ old('color', $product->color) }}"
                required
            >

        </div>


        {{-- CATEGORY --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Category
            </label>

            <input
                type="text"
                name="category"
                class="form-control"
                value="{{ old('category', $product->category) }}"
                required
            >

        </div>


        {{-- PRICE --}}

        <div class="mb-3">

            <label class="form-label fw-bold">
                Price
            </label>

            <input
                type="number"
                name="price"
                class="form-control"
                value="{{ old('price', $product->price) }}"
                min="0"
                required
            >

        </div>


        {{-- BUTTONS --}}

        <button
            type="submit"
            class="btn btn-primary"
        >
            Update Product
        </button>


        <a
            href="{{ route('products.index') }}"
            class="btn btn-secondary"
        >
            Back
        </a>

    </form>

</div>

@endsection


@push('styles')

<style>

.upload-box {

    border-style: dashed !important;

    cursor: pointer;

    background: #f8f9fa;

    transition: 0.2s;

}

.upload-box:hover {

    background: #e9ecef;

}


.existing-image-card {

    width: 180px;

    margin: 8px;

    padding: 8px;

    border: 2px solid #dee2e6;

    border-radius: 8px;

    background: white;

    transition: 0.2s;

}


.primary-image-card {

    border: 3px solid #0d6efd;

}


.existing-image {

    width: 160px;

    height: 120px;

    object-fit: cover;

    border-radius: 6px;

}


.primary-label {

    position: absolute;

    top: 5px;

    left: 5px;

    background: #0d6efd;

    color: white;

    padding: 3px 7px;

    border-radius: 4px;

    font-size: 11px;

}


.image-actions {

    display: flex;

    flex-direction: column;

    gap: 5px;

    margin-top: 8px;

}


.image-preview-item {

    position: relative;

    width: 150px;

    height: 175px;

    margin: 5px;

    border-radius: 8px;

    overflow: hidden;

    border: 2px solid #dee2e6;

    background: white;

}


.image-preview-item.primary {

    border: 3px solid #0d6efd;

}


.image-preview-item img {

    width: 100%;

    height: 130px;

    object-fit: cover;

}


.primary-badge {

    position: absolute;

    top: 5px;

    left: 5px;

    background: #0d6efd;

    color: white;

    padding: 3px 7px;

    border-radius: 4px;

    font-size: 11px;

    z-index: 2;

}


.remove-btn {

    position: absolute;

    top: 5px;

    right: 5px;

    background: #dc3545;

    color: white;

    border: none;

    border-radius: 50%;

    width: 25px;

    height: 25px;

    cursor: pointer;

    z-index: 2;

}


.preview-actions {

    height: 42px;

    display: flex;

    justify-content: center;

    align-items: center;

}

</style>

@endpush


@push('scripts')

<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */

    let selectedFiles = [];

    let primaryNewImageIndex = null;


    /*
    |--------------------------------------------------------------------------
    | Existing Image Primary Selection
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        'input[name="primary_image"]',
        function () {

            $('.existing-image-card')
                .removeClass(
                    'primary-image-card'
                );


            $(this)
                .closest(
                    '.existing-image-card'
                )
                .addClass(
                    'primary-image-card'
                );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Existing Image Delete
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.delete-image-checkbox',
        function () {

            const card =
                $(this).closest(
                    '.existing-image-card'
                );


            if ($(this).is(':checked')) {

                card.css(
                    'opacity',
                    '0.45'
                );

            } else {

                card.css(
                    'opacity',
                    '1'
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | New Image Upload
    |--------------------------------------------------------------------------
    */

    $('#productImages').on(
        'change',
        function (e) {

            const files =
                Array.from(
                    e.target.files
                );


            files.forEach(
                function (file) {

                    if (
                        !file.type
                            .startsWith('image/')
                    ) {

                        return;

                    }


                    selectedFiles.push(file);


                    const index =
                        selectedFiles.length - 1;


                    const reader =
                        new FileReader();


                    reader.onload =
                        function (event) {

                            showPreview(
                                event.target.result,
                                file.name,
                                index
                            );

                        };


                    reader.readAsDataURL(
                        file
                    );

                }
            );


            /*
            | Keep input empty while
            | selecting multiple times.
            |
            | Before form submit we restore
            | all files using DataTransfer.
            */

            $(this).val('');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Show New Image Preview
    |--------------------------------------------------------------------------
    */

    function showPreview(
        src,
        filename,
        index
    ) {

        const div =
            document.createElement(
                'div'
            );


        div.className =
            'image-preview-item';


        div.dataset.index =
            index;


        div.innerHTML = `

            <span
                class="primary-badge"
                style="display:none;"
            >
                PRIMARY
            </span>


            <img
                src="${src}"
                alt="${filename}"
            >


            <button
                type="button"
                class="remove-btn"
            >
                &times;
            </button>


            <div class="preview-actions">

                <button
                    type="button"
                    class="
                    btn
                    btn-sm
                    btn-outline-primary
                    primary-btn
                    "
                >
                    ⭐ Primary
                </button>

            </div>

        `;


        /*
        |--------------------------------------------------------------------------
        | Primary Button
        |--------------------------------------------------------------------------
        */

        div.querySelector(
            '.primary-btn'
        ).addEventListener(
            'click',
            function () {

                setNewPrimary(index);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Remove Button
        |--------------------------------------------------------------------------
        */

        div.querySelector(
            '.remove-btn'
        ).addEventListener(
            'click',
            function () {

                selectedFiles.splice(
                    index,
                    1
                );


                div.remove();


                rebuildIndexes();

            }
        );


        document
            .getElementById(
                'imagePreviewContainer'
            )
            .appendChild(div);

    }


    /*
    |--------------------------------------------------------------------------
    | Set New Image as Primary
    |--------------------------------------------------------------------------
    */

    function setNewPrimary(index) {

        primaryNewImageIndex =
            index;


        $('#primaryImageIndex')
            .val(index);


        document
            .querySelectorAll(
                '.image-preview-item'
            )
            .forEach(
                function (item) {

                    item.classList.remove(
                        'primary'
                    );


                    item.querySelector(
                        '.primary-badge'
                    ).style.display =
                        'none';


                    item.querySelector(
                        '.primary-btn'
                    ).classList.remove(
                        'btn-primary'
                    );


                    item.querySelector(
                        '.primary-btn'
                    ).classList.add(
                        'btn-outline-primary'
                    );

                }
            );


        const selected =
            document.querySelector(
                `.image-preview-item[data-index="${index}"]`
            );


        if (selected) {

            selected.classList.add(
                'primary'
            );


            selected.querySelector(
                '.primary-badge'
            ).style.display =
                'block';


            selected.querySelector(
                '.primary-btn'
            ).classList.remove(
                'btn-outline-primary'
            );


            selected.querySelector(
                '.primary-btn'
            ).classList.add(
                'btn-primary'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Rebuild New Image Indexes
    |--------------------------------------------------------------------------
    */

    function rebuildIndexes() {

        const items =
            document.querySelectorAll(
                '.image-preview-item'
            );


        items.forEach(
            function (item, index) {

                item.dataset.index =
                    index;

            }
        );


        /*
        | No files remaining
        */

        if (
            selectedFiles.length === 0
        ) {

            primaryNewImageIndex =
                null;


            $('#primaryImageIndex')
                .val('');


            return;

        }


        /*
        | If selected primary was deleted,
        | select first remaining image.
        */

        if (
            primaryNewImageIndex === null ||
            primaryNewImageIndex >=
                selectedFiles.length
        ) {

            primaryNewImageIndex =
                0;

        }


        setNewPrimary(
            primaryNewImageIndex
        );

    }


    /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    |
    | Restore selected files into
    | the actual file input before
    | submitting the form.
    |--------------------------------------------------------------------------
    */

    $('#editProductForm').on(
        'submit',
        function () {

            const fileInput =
                document.getElementById(
                    'productImages'
                );


            const dataTransfer =
                new DataTransfer();


            selectedFiles.forEach(
                function (file) {

                    dataTransfer.items.add(
                        file
                    );

                }
            );


            fileInput.files =
                dataTransfer.files;


            /*
            | Make sure primary index
            | is submitted.
            */

            if (
                primaryNewImageIndex !== null
            ) {

                $('#primaryImageIndex')
                    .val(
                        primaryNewImageIndex
                    );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Select2
    |--------------------------------------------------------------------------
    */

    $('#tagSelect').select2({

        placeholder:
            'Select Tags',

        allowClear:
            true,

        closeOnSelect:
            true,

        width:
            '100%'

    });

});

</script>

@endpush