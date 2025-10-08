@extends('layouts.app')

@section('content')
<div class="container">
    <h3>➕ Add New Movie</h3>

    
    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Genre</label>
            <input type="text" name="genre" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control"></textarea>
        </div>

        
        <div class="mb-3">
            <label class="form-label">Poster (optional)</label>
            <input type="file" name="poster" class="form-control">
            <small class="text-muted">รองรับ JPG, PNG, JPEG (สูงสุด 2MB)</small>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.movies') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection