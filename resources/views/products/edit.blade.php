@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- NAME -->
        <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $product->name) }}" required>
        </div>

        <!-- DETAILS -->
        <div class="mb-3">
            <label class="form-label fw-bold">Details</label>
            <textarea name="details" class="form-control" required>{{ old('details', $product->details) }}</textarea>
        </div>

        <!-- TAGS MULTIPLE SELECT (SELECT2) -->
        @php
            $selectedTags = $product->tag_ids
                ? (is_array($product->tag_ids) ? $product->tag_ids : json_decode($product->tag_ids, true))
                : [];
        @endphp

        <div class="mb-3">
            <label class="form-label fw-bold">Select Tags</label>

            <select name="tag_ids[]" id="tagSelect" class="form-select" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}"
                        {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>
                        {{ $tag->tag_name }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">You can select multiple tags</small>
        </div>

        <!-- EXISTING IMAGES -->
        @php
            $existingImages = $product->images 
                ? (is_array($product->images) ? $product->images : json_decode($product->images, true))
                : [];
        @endphp

        <div class="mb-3">
            <label class="form-label fw-bold">Existing Images</label>
            <div class="d-flex flex-wrap">

                @if(!empty($existingImages))
                    @foreach($existingImages as $img)
                        <div class="m-2 text-center">
                            <img src="{{ asset($img) }}" width="80" class="rounded border mb-1">

                            <br>
                            <input type="checkbox" name="delete_images[]" value="{{ $img }}">
                            <label class="text-danger small">Delete</label>
                        </div>
                    @endforeach
                @else
                    <span class="text-muted">No Images Found</span>
                @endif

            </div>
        </div>

        <!-- ADD NEW IMAGES -->
        <div class="mb-3">
            <label class="form-label fw-bold">Add New Images</label>

            <div id="imageRepeater">
                <div class="row mb-2 repeater-item">

                    <div class="col-md-5">
                        <input type="file" name="images[]" class="form-control image-input" accept="image/*">
                    </div>

                    <div class="col-md-5">
                        <img src="" class="img-preview" width="80"
                             style="display:none; border:1px solid #ddd; padding:3px; border-radius:5px;">
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger removeRow">Remove</button>
                    </div>

                </div>
            </div>

            <button type="button" id="addImage" class="btn btn-secondary mt-2">+ Add More Images</button>
        </div>

        <!-- SIZE -->
        <div class="mb-3">
            <label class="form-label fw-bold">Size</label>
            <input type="text" name="size" class="form-control"
                   value="{{ old('size', $product->size) }}" required>
        </div>

        <!-- COLOR -->
        <div class="mb-3">
            <label class="form-label fw-bold">Color</label>
            <input type="text" name="color" class="form-control"
                   value="{{ old('color', $product->color) }}" required>
        </div>

        <!-- CATEGORY -->
        <div class="mb-3">
            <label class="form-label fw-bold">Category</label>
            <input type="text" name="category" class="form-control"
                   value="{{ old('category', $product->category) }}" required>
        </div>

        <!-- PRICE -->
        <div class="mb-3">
            <label class="form-label fw-bold">Price</label>
            <input type="number" name="price" class="form-control"
                   value="{{ old('price', $product->price) }}" required>
        </div>

        <!-- SUBMIT -->
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back</a>

    </form>
</div>
@endsection


@push('scripts')

<!-- SELECT2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// INIT SELECT2 WITH AUTO CLOSE
$(document).ready(function() {
    $('#tagSelect').select2({
        placeholder: "Select Tags",
        allowClear: true,
        closeOnSelect: true,  // ⭐ Auto close after selecting
        width: "100%"
    });
});

// ADD IMAGE ROW
document.getElementById('addImage').onclick = function () {
    let html = `
        <div class="row mb-2 repeater-item">
            <div class="col-md-5">
                <input type="file" name="images[]" class="form-control image-input" accept="image/*">
            </div>

            <div class="col-md-5">
                <img src="" class="img-preview" width="80"
                    style="display:none; border:1px solid #ddd; padding:3px; border-radius:5px;">
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeRow">Remove</button>
            </div>
        </div>
    `;
    document.getElementById('imageRepeater').insertAdjacentHTML('beforeend', html);
};

// REMOVE IMAGE ROW
document.addEventListener('click', e => {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.repeater-item').remove();
    }
});

// IMAGE PREVIEW
document.addEventListener('change', function(e) {

    if (e.target.classList.contains('image-input')) {

        let file = e.target.files[0];
        let preview = e.target.closest('.repeater-item').querySelector('.img-preview');

        if (file) {
            let reader = new FileReader();
            reader.onload = function(evt) {
                preview.src = evt.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

@endpush
