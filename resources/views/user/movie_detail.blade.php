@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4">
        <img src="{{ $movie->poster ? asset('storage/'.$movie->poster) : 'https://via.placeholder.com/300x400' }}" class="img-fluid rounded">
    </div>
    <div class="col-md-8">
        <h2>{{ $movie->title }}</h2>
        <p><strong>Genre:</strong> {{ $movie->genre }}</p>
        <p><strong>Duration:</strong> {{ $movie->duration }} minutes</p>
        <p>{{ $movie->description }}</p>

        @auth
            <a href="{{ route('booking.seat', $movie->id) }}" class="btn btn-danger">Book Now</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-light">Login to Book</a>
        @endauth
    </div>
</div>

@if($showtimes->count())
    <hr>
    <h5>Available Showtimes</h5>
    <ul>
        @foreach($showtimes as $show)
            <li>{{ $show->show_date }} at {{ $show->start_time }} (Theatre {{ $show->theatre_id }})</li>
        @endforeach
    </ul>
@endif
@endsection
