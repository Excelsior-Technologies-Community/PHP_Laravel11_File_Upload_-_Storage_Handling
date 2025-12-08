@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Create Tag</h2>

    <form action="{{ route('tags.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Tag Name</label>
            <input type="text" name="tag_name" class="form-control" required>
        </div>

        <button class="btn btn-primary">Create Tag</button>
    </form>
</div>
@endsection
