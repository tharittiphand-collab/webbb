@extends('layouts.app')

@section('content')
<div class="container mt-4 text-white">
    <div class="row">
        <div class="col-12">
            <h2>✏️ Edit Movie: {{ $movie->title }}</h2>
            
            @if (session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            
            <a href="{{ route('admin.movies') }}" class="btn btn-secondary mb-3">← Back to Movies</a>
        </div>
    </div>

    <div class="row">
        {{-- ================= Edit Movie Form ================= --}}
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header">
                    <h4>🎬 Movie Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        {{-- Current Poster Preview --}}
                        <div class="mb-3">
                            <label class="form-label">Current Poster</label>
                            <div>
                                @if($movie->poster)
                                    <img src="{{ asset('storage/'.$movie->poster) }}" class="img-thumbnail" width="150">
                                @else
                                    <span class="text-muted">No Poster</span>
                                @endif
                            </div>
                        </div>

                        {{-- Upload New Poster --}}
                        <div class="mb-3">
                            <label class="form-label">Upload New Poster (Optional)</label>
                            <input type="file" name="poster" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current poster</small>
                        </div>

                        {{-- Movie Title --}}
                        <div class="mb-3">
                            <label class="form-label">Movie Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $movie->title }}" required>
                        </div>

                        {{-- Genre --}}
                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <input type="text" name="genre" class="form-control" value="{{ $movie->genre }}">
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ $movie->description }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">💾 Update Movie</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= Showtimes Management ================= --}}
        <div class="col-md-6">
            <div class="card bg-dark">
                <div class="card-header">
                    <h4>🕒 Showtimes for this Movie</h4>
                </div>
                <div class="card-body">
                    {{-- Current Showtimes List --}}
                    @if($showtimes->count() > 0)
                        <div class="mb-3">
                            <h6>Current Showtimes:</h6>
                            @foreach($showtimes as $showtime)
                                <div class="d-flex justify-content-between align-items-center bg-secondary p-2 mb-2 rounded">
                                    <div>
                                        <strong>{{ $showtime->theatre->name }}</strong><br>
                                        <small>{{ $showtime->show_date }} | {{ $showtime->start_time }} - {{ $showtime->end_time }}</small>
                                    </div>
                                    <form method="POST" action="{{ route('admin.showtimes.destroy', $showtime->id) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                    @else
                        <p class="text-muted">No showtimes yet for this movie.</p>
                        <hr>
                    @endif

                    {{-- Add New Showtime --}}
                    <h6>➕ Add New Showtime</h6>
                    <form action="{{ route('admin.showtimes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                        
                        <div class="mb-2">
                            <label class="form-label">Theatre</label>
                            <select name="theatre_id" class="form-select" required>
                                <option value="">Select Theatre</option>
                                @foreach($theatres as $theatre)
                                    <option value="{{ $theatre->id }}">{{ $theatre->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Show Date</label>
                            <input type="date" name="show_date" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">➕ Add Showtime</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Quick Theatre Management ================= --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-dark">
                <div class="card-header">
                    <h4>🏠 Available Theatres</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-wrap">
                                @foreach($theatres as $theatre)
                                    <span class="badge bg-info me-2 mb-2 p-2">{{ $theatre->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('admin.theatres.store') }}" method="POST" class="d-flex">
                                @csrf
                                <input name="name" class="form-control me-2" placeholder="New Theatre Name" required>
                                <button class="btn btn-success">➕</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="show_date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
});
</script>
@endsection