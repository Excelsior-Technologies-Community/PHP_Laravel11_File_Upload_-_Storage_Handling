@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Create Product</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
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

        <!-- IMAGE UPLOAD -->
        <div class="mb-3">
            <label class="form-label fw-bold">Product Images</label>
            
            <div class="border rounded p-4 text-center" 
                 style="border-style: dashed !important; cursor: pointer; background: #f8f9fa;"
                 onclick="document.getElementById('productImages').click()">
                <div class="py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="bi bi-cloud-upload mb-2" viewBox="0 0 16 16">
                        <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5C10.876 4.725 9.674 3.5 8 3.5a5.5 5.5 0 0 0-4.578 2.684C2.877 7.336 1.5 9.037 1.5 11c0 .563.208 1.09.574 1.521L1 13.654A.5.5 0 0 1 1.5 14h11a.5.5 0 0 1 .5.5v1.637l-.574.466C12.552 16.91 13 17.37 13 18c0 .938-.804 1.785-1.79 1.861C10.049 20.141 8.5 19.5 8.5 19.5S6.964 20.141 5.793 19.861C4.74 19.785 4 18.938 4 18c0-.63.448-1.09 1.074-1.37L2.5 13.5v-1.637c0-.563.208-1.09.574-1.521L4 11.364c-.05.373-.096.737-.096 1.115 0 .553.448 1 1 1h6.5a.5.5 0 0 0 .5-.5c0-.558-.448-1-1-1H8.5a.5.5 0 0 1 0-1H12.5c.552 0 1 .448 1 1v2.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-2.5c0-.552.448-1 1-1h3.5a.5.5 0 0 0 0-1H5.5c-.552 0-1-.448-1-1V3.342zM8 4.5a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                    <p class="mb-1 fw-semibold">Click to upload images</p>
                    <small class="text-muted">JPG, PNG, GIF up to 2MB per image</small>
                </div>
            </div>
            
            <input type="file" name="images[]" id="productImages" class="form-control d-none" multiple accept="image/*">
            
            <div id="imagePreviewContainer" class="d-flex flex-wrap mt-3"></div>
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

@push('styles')
<style>
    .image-preview-item {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 5px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #dee2e6;
    }
    
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-preview-item .remove-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedFiles = [];
    
    $('#productImages').on('change', function(e) {
        let files = Array.from(e.target.files);
        
        files.forEach(function(file) {
            if (!file.type.startsWith('image/')) return;
            
            let reader = new FileReader();
            reader.onload = function(e) {
                selectedFiles.push(file);
                showPreview(e.target.result, file.name);
            };
            reader.readAsDataURL(file);
        });
        
        $(this).val('');
    });
    
    function showPreview(src, filename) {
        let div = document.createElement('div');
        div.className = 'image-preview-item';
        div.innerHTML = `
            <img src="${src}" alt="${filename}">
            <button type="button" class="remove-btn">&times;</button>
        `;
        
        div.querySelector('.remove-btn').addEventListener('click', function() {
            div.remove();
            selectedFiles = selectedFiles.filter(f => f.name !== filename);
        });
        
        document.getElementById('imagePreviewContainer').appendChild(div);
    }
    
    $('.select2-tags').select2({
        placeholder: "Select product tags",
        allowClear: true,
        closeOnSelect: true,
        width: "100%"
    });
});
</script>
@endpush