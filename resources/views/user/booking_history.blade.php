@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="text-white mb-3"> My Booking History</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-dark table-bordered align-middle">
        <thead>
            <tr>
                <th>Movie</th>
                <th>Theatre</th> <!-- ✅ เพิ่ม -->
                <th>Showtime</th>
                <th>Seat</th>
                <th>Status</th>
                <th>Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td>{{ $b->movie->title ?? '-' }}</td>
                    <td>{{ $b->showtime->theatre->name ?? '-' }}</td> <!-- ✅ เพิ่ม -->
                    <td>{{ $b->showtime->show_date ?? '-' }} {{ $b->showtime->start_time ?? '' }}</td>
                    <td>{{ $b->seat_number }}</td>

                    <td>
                        @if($b->status === 'Paid')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>

                    <td>{{ number_format($b->amount, 2) }} ฿</td>

                    <td>
                        @if($b->status !== 'Paid')
                            <a href="{{ route('booking.payment', $b->id) }}" class="btn btn-primary btn-sm">Pay now</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No bookings found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

