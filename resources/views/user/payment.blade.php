@extends('layouts.app')

@section('content')
<h3>💳 Payment for {{ $booking->movie->title }}</h3>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card bg-dark text-white p-4">
            <h5>Booking Details</h5>
            <p><strong>Showtime:</strong> {{ $booking->showtime->show_date ?? '-' }} {{ $booking->showtime->start_time ?? '' }}</p>
            <p><strong>Seat Number:</strong> {{ $booking->seat_number }}</p>
            <p><strong>Amount:</strong> {{ number_format($booking->amount, 2) }} ฿</p>
            <p><strong>Status:</strong>
                @if($booking->status === 'Paid')
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-warning text-dark">Pending</span>
                @endif
            </p>
        </div>
    </div>

    <div class="col-md-6 text-center">
        <h5>Scan to Pay</h5>

        <div class="bg-white d-inline-block p-3 rounded">
            @if($qrImageUrl)
                <img src="{{ $qrImageUrl }}" alt="QR Code" width="250" height="250">
            @else
                <div class="text-danger">ไม่สามารถสร้าง QR Code ได้</div>
            @endif
        </div>

        @if($booking->status !== 'Paid')
        <form method="POST" action="{{ route('booking.updateStatus', $booking->id) }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-success">I have paid</button>
        </form>
        @else
            <div class="alert alert-success mt-4">✔ Payment already completed.</div>
        @endif
    </div>
</div>
@endsection