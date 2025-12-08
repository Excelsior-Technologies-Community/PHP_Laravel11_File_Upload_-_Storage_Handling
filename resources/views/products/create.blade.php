@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Create Product</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- NAME -->
        <div class="mb-3">
            <label class="form-label fw-bold">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <!-- DETAILS -->
        <div class="mb-3">
            <label class="form-label fw-bold">Details</label>
            <textarea name="details" class="form-control" required></textarea>
        </div>

        <!-- TAG SELECT (Select2) -->
        <div class="form-group mb-3">
            <label class="form-label fw-bold">Select Tags:</label>

            <select name="tag_ids[]" id="tagSelect" class="form-control select2-tags" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                @endforeach
            </select>

            <small class="text-muted">Select multiple tags (click again to open).</small>
        </div>

        <!-- MULTIPLE IMAGES UPLOAD -->
        <div class="mb-3">
            <label class="form-label fw-bold">Product Images</label>

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
            <input type="text" name="size" class="form-control" required>
        </div>

        <!-- COLOR -->
        <div class="mb-3">
            <label class="form-label fw-bold">Color</label>
            <input type="text" name="color" class="form-control" required>
        </div>

        <!-- CATEGORY -->
        <div class="mb-3">
            <label class="form-label fw-bold">Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>

        <!-- PRICE -->
        <div class="mb-3">
            <label class="form-label fw-bold">Price</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-2">Back</a>

    </form>
</div>
@endsection


@push('scripts')

<!-- SELECT2 CSS + JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // ⭐ TAG SELECT2 INIT
    $('.select2-tags').select2({
        placeholder: "Select product tags",
        allowClear: true,
        closeOnSelect: true,  // ⭐ same behaviour as your example
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
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.repeater-item').remove();
    }
});

// IMAGE PREVIEW
document.addEventListener('change', function(e) {

    if (e.target.classList.contains('image-input')) {

        let file = e.target.files[0];
        let previewImg = e.target.closest('.repeater-item').querySelector('.img-preview');

        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

@endpush
