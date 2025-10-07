@extends('layouts.app')

@section('content')

@if($recommendations->count() > 0)
<h3 class="mb-3"> Recommended for You</h3>
<div class="row mb-4">
@foreach($recommendations as $rec)
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <img src="{{ $rec->poster ? asset('storage/'.$rec->poster) : 'https://via.placeholder.com/200x300' }}" class="card-img-top" alt="{{ $rec->title }}">
            <div class="card-body text-dark">
                <h5>{{ $rec->title }}</h5>
                <p class="text-muted">{{ $rec->genre }}</p>
                <a href="{{ route('movie.show', $rec->id) }}" class="btn btn-danger btn-sm">View Detail</a>
            </div>
        </div>
    </div>
@endforeach
</div>
@endif

<h3 class="mb-3"> Now Showing</h3>
<div class="row">
@foreach($movies as $movie)
    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <img src="{{ $movie->poster ? asset('storage/'.$movie->poster) : 'https://via.placeholder.com/200x300' }}" class="card-img-top" alt="{{ $movie->title }}">
            <div class="card-body text-dark">
                <h5>{{ $movie->title }}</h5>
                <p class="text-muted">{{ $movie->genre }}</p>
                <a href="{{ route('movie.show', $movie->id) }}" class="btn btn-danger btn-sm">View Detail</a>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection
