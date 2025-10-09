@extends('layouts.app')

@section('content')
<div class="container">
    <h3>✏️ Edit Movie</h3>

    
    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $movie->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Genre</label>
            <input type="text" name="genre" class="form-control" value="{{ $movie->genre }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ $movie->description }}</textarea>
        </div>

        <!-- ✅ แสดงโปสเตอร์ปัจจุบัน -->
        <div class="mb-3">
            <label class="form-label">Current Poster</label><br>
            @if($movie->poster)
                <img src="{{ asset('storage/' . $movie->poster) }}" alt="Poster" width="150" class="rounded mb-2 border">
            @else
                <p class="text-muted">No poster uploaded.</p>
            @endif
        </div>

        <!-- ✅ อัปโหลดโปสเตอร์ใหม่ -->
        <div class="mb-3">
            <label class="form-label">Change Poster (optional)</label>
            <input type="file" name="poster" class="form-control">
            <small class="text-muted">รองรับ JPG, PNG, JPEG (สูงสุด 2MB)</small>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.movies') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
