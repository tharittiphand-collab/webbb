@extends('layouts.app')

@section('content')
<h3>🕒 Manage Showtimes</h3>

<form method="POST" action="{{ route('admin.showtimes.store') }}" class="row g-2 mb-4">
    @csrf
    <div class="col-md-3">
        <select name="movie_id" class="form-select" required>
            <option value="">-- Select Movie --</option>
            @foreach($movies as $m)
                <option value="{{ $m->id }}">{{ $m->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="theatre_id" class="form-select" required>
            <option value="">-- Select Theatre --</option>
            @foreach($theatres as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="show_date" class="form-control" required>
    </div>
    <div class="col-md-2">
        <input type="time" name="start_time" class="form-control" required>
    </div>
    <div class="col-md-2">
        <input type="time" name="end_time" class="form-control" required>
    </div>
    <div class="col-md-12 text-end mt-2">
        <button type="submit" class="btn btn-success">Add Showtime</button>
    </div>
</form>

<table class="table table-dark table-bordered">
    <thead>
        <tr>
            <th>Movie</th>
            <th>Theatre</th>
            <th>Date</th>
            <th>Start</th>
            <th>End</th>
        </tr>
    </thead>
    <tbody>
        @forelse($showtimes as $s)
        <tr>
            <td>{{ $s->movie->title }}</td>
            <td>{{ $s->theatre->name }}</td>
            <td>{{ $s->show_date }}</td>
            <td>{{ $s->start_time }}</td>
            <td>{{ $s->end_time }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">No showtimes added yet.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
