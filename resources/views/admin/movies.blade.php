@extends('layouts.app')

@section('content')
<div class="container mt-4 text-white">

    <h2>🎬 Admin Management</h2>
    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    {{-- ==================== MOVIES ==================== --}}
    <hr>
    <h4>🎞️ Movies</h4>

    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Title</th>
                <th>Genre</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movies as $movie)
                <tr>
                    <td>{{ $movie->id }}</td>
                    <td>
                        @if($movie->poster)
                            <img src="{{ asset('storage/'.$movie->poster) }}" width="60">
                        @else
                            <span class="text-muted">No Poster</span>
                        @endif
                    </td>
                    <td>{{ $movie->title }}</td>
                    <td>{{ $movie->genre }}</td>
                    <td>{{ Str::limit($movie->description, 50) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.movies.destroy', $movie->id) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data" class="bg-dark p-3 rounded">
        @csrf
        <h5>➕ Add Movie</h5>
        <input name="title" class="form-control mb-2" placeholder="Title" required>
        <input name="genre" class="form-control mb-2" placeholder="Genre">
        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
        <input type="file" name="poster" class="form-control mb-2">
        <button class="btn btn-success">Add Movie</button>
    </form>


    {{-- ==================== THEATRES ==================== --}}
    <hr>
    <h4>🏠 Theatres</h4>

    <table class="table table-dark table-striped align-middle">
        <thead><tr><th>ID</th><th>Name</th><th></th></tr></thead>
        <tbody>
            @foreach ($theatres as $theatre)
                <tr>
                    <td>{{ $theatre->id }}</td>
                    <td>{{ $theatre->name }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.theatres.destroy', $theatre->id) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('admin.theatres.store') }}" method="POST" class="bg-dark p-3 rounded">
        @csrf
        <h5>➕ Add Theatre</h5>
        <input name="name" class="form-control mb-2" placeholder="Theatre Name" required>
        <button class="btn btn-success">Add Theatre</button>
    </form>


    {{-- ==================== SHOWTIMES ==================== --}}
    <hr>
    <h4>🕒 Showtimes</h4>

    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr><th>Movie</th><th>Theatre</th><th>Date</th><th>Start</th><th>End</th><th></th></tr>
        </thead>
        <tbody>
            @foreach ($showtimes as $show)
                <tr>
                    <td>{{ $show->movie->title }}</td>
                    <td>{{ $show->theatre->name }}</td>
                    <td>{{ $show->show_date }}</td>
                    <td>{{ $show->start_time }}</td>
                    <td>{{ $show->end_time }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.showtimes.destroy', $show->id) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('admin.showtimes.store') }}" method="POST" class="bg-dark p-3 rounded">
        @csrf
        <h5>➕ Add Showtime</h5>
        <select name="movie_id" class="form-select mb-2" required>
            <option value="">Select Movie</option>
            @foreach ($movies as $m)
                <option value="{{ $m->id }}">{{ $m->title }}</option>
            @endforeach
        </select>

        <select name="theatre_id" class="form-select mb-2" required>
            <option value="">Select Theatre</option>
            @foreach ($theatres as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>

        <input type="date" name="show_date" class="form-control mb-2" required>
        <input type="time" name="start_time" class="form-control mb-2" required>
        <input type="time" name="end_time" class="form-control mb-2" required>

        <button class="btn btn-success">Add Showtime</button>
    </form>
</div>
@endsection

