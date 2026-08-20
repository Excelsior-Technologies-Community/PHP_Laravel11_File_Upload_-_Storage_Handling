@extends('layouts.admin')

@section('content')

<div class="container">

    <h1 class="mb-4">Create Product</h1>

    <form
        action="{{ route('products.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="productForm"
    >

        @csrf

        <!-- NAME -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Name
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ old('name') }}"
                required
            >

        </div>


        <!-- DETAILS -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Details
            </label>

            <textarea
                name="details"
                class="form-control"
                required
            >{{ old('details') }}</textarea>

        </div>


        <!-- TAGS -->
        <div class="form-group mb-3">

            <label class="form-label fw-bold">
                Select Tags:
            </label>

            <select
                name="tag_ids[]"
                id="tagSelect"
                class="form-control"
                multiple
            >

                @foreach($tags as $tag)

                    <option value="{{ $tag->id }}">
                        {{ $tag->tag_name }}
                    </option>

                @endforeach

            </select>

        </div>


        <!-- IMAGES -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Product Images
            </label>

            <div
                class="border rounded p-4 text-center upload-box"
                onclick="document.getElementById('productImages').click()"
            >

                <div class="py-3">

                    <div style="font-size: 40px;">
                        📤
                    </div>

                    <p class="mb-1 fw-semibold">
                        Click to upload images
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


            <!-- NEW: PRIMARY IMAGE -->
            <input
                type="hidden"
                name="primary_image_index"
                id="primaryImageIndex"
                value="0"
            >


            <div
                id="imagePreviewContainer"
                class="d-flex flex-wrap mt-3"
            ></div>

            <small class="text-muted">
                <strong>Tip:</strong>
                Click the "Primary" button on an image to make it
                the main product image.
            </small>

        </div>


        <!-- SIZE -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Size
            </label>

            <input
                type="text"
                name="size"
                class="form-control"
                value="{{ old('size') }}"
                required
            >

        </div>


        <!-- COLOR -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Color
            </label>

            <input
                type="text"
                name="color"
                class="form-control"
                value="{{ old('color') }}"
                required
            >

        </div>


        <!-- CATEGORY -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Category
            </label>

            <input
                type="text"
                name="category"
                class="form-control"
                value="{{ old('category') }}"
                required
            >

        </div>


        <!-- PRICE -->
        <div class="mb-3">

            <label class="form-label fw-bold">
                Price
            </label>

            <input
                type="number"
                name="price"
                class="form-control"
                value="{{ old('price') }}"
                min="0"
                required
            >

        </div>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Create Product
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
    background: #eef5ff;
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

}

.preview-actions {

    height: 42px;

    display: flex;

    align-items: center;

    justify-content: center;

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

</style>

@endpush


@push('scripts')

<script>

$(document).ready(function () {

    let selectedFiles = [];

    /*
    |--------------------------------------------------------------------------
    | IMAGE SELECTION
    |--------------------------------------------------------------------------
    */

    $('#productImages').on('change', function (e) {

        const files = Array.from(e.target.files);

        files.forEach(function (file) {

            if (!file.type.startsWith('image/')) {
                return;
            }

            selectedFiles.push(file);

            const index = selectedFiles.length - 1;

            const reader = new FileReader();

            reader.onload = function (event) {

                showPreview(
                    event.target.result,
                    file.name,
                    index
                );

            };

            reader.readAsDataURL(file);

        });

        /*
        | Don't clear the input here.
        | We will rebuild it before form submission.
        */

    });


    /*
    |--------------------------------------------------------------------------
    | SHOW IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    function showPreview(src, filename, index) {

        const div = document.createElement('div');

        div.className = 'image-preview-item';

        div.dataset.index = index;

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
                    class="btn btn-sm btn-outline-primary primary-btn"
                >
                    ⭐ Primary
                </button>

            </div>

        `;


        /*
        |--------------------------------------------------------------------------
        | PRIMARY BUTTON
        |--------------------------------------------------------------------------
        */

        div.querySelector('.primary-btn')
            .addEventListener('click', function () {

                setPrimary(index);

            });


        /*
        |--------------------------------------------------------------------------
        | REMOVE BUTTON
        |--------------------------------------------------------------------------
        */

        div.querySelector('.remove-btn')
            .addEventListener('click', function () {

                const currentIndex =
                    parseInt(div.dataset.index);

                selectedFiles.splice(
                    currentIndex,
                    1
                );

                div.remove();

                rebuildIndexes();

            });


        document
            .getElementById('imagePreviewContainer')
            .appendChild(div);


        /*
        |--------------------------------------------------------------------------
        | FIRST IMAGE = PRIMARY
        |--------------------------------------------------------------------------
        */

        if (selectedFiles.length === 1) {

            setPrimary(0);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SET PRIMARY IMAGE
    |--------------------------------------------------------------------------
    */

    function setPrimary(index) {

        $('#primaryImageIndex').val(index);


        document
            .querySelectorAll('.image-preview-item')
            .forEach(function (item) {

                item.classList.remove('primary');

                item.querySelector(
                    '.primary-badge'
                ).style.display = 'none';

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

            });


        const selected =
            document.querySelector(
                `.image-preview-item[data-index="${index}"]`
            );


        if (selected) {

            selected.classList.add('primary');

            selected.querySelector(
                '.primary-badge'
            ).style.display = 'block';

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
    | REBUILD IMAGE INDEXES AFTER DELETE
    |--------------------------------------------------------------------------
    */

    function rebuildIndexes() {

        document
            .querySelectorAll('.image-preview-item')
            .forEach(function (item, index) {

                item.dataset.index = index;

            });


        if (selectedFiles.length === 0) {

            $('#primaryImageIndex').val('');

        } else {

            /*
            | First remaining image becomes primary.
            */

            setPrimary(0);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | PUT SELECTED FILES BACK INTO INPUT BEFORE SUBMIT
    |--------------------------------------------------------------------------
    */

    $('#productForm').on('submit', function () {

        const dataTransfer = new DataTransfer();


        selectedFiles.forEach(function (file) {

            dataTransfer.items.add(file);

        });


        document.getElementById(
            'productImages'
        ).files = dataTransfer.files;

    });


    /*
    |--------------------------------------------------------------------------
    | SELECT2
    |--------------------------------------------------------------------------
    */

    $('#tagSelect').select2({

        placeholder: "Select product tags",

        allowClear: true,

        closeOnSelect: true,

        width: "100%"

    });

});

</script>

@endpush