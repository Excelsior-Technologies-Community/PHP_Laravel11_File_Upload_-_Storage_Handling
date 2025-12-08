@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Tag</h2>

    <form action="{{ route('tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tag Name</label>
            <input type="text" name="tag_name" class="form-control" 
                   value="{{ $tag->tag_name }}" required>
        </div>

        <button class="btn btn-primary">Update Tag</button>
    </form>
</div>
@endsection
