@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tag List</h2>

    <a href="{{ route('tags.create') }}" class="btn btn-primary mb-3">Add New Tag</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tag Name</th>
                <th width="20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
                <tr>
                    <td>{{ $tag->tag_name }}</td>
                    <td>
                        <a href="{{ route('tags.edit', $tag) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="d-inline">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $tags->links() }}
</div>
@endsection
