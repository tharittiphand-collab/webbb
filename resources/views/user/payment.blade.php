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

        @php
            // เลือก payload ที่ต้องการใช้ฝังใน QR
            // 1) ใช้ข้อความธรรมดา (อ่านง่าย)
            $payload = $qrPayloadText;

            // 2) หรือใช้เป็น URL ก็ได้ (สแกนแล้วเปิดหน้าเว็บ)
            // $payload = $qrPayloadUrl;

            $qrData = urlencode($payload);
            // ขนาด QR: chs=250x250, ประเภท: cht=qr, ข้อมูล: chl=...
            // เพิ่มระดับแก้ error ได้ด้วย chld=L|M|Q|H (เช่น H = สูงสุด)
            $qrUrl  = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chld=H&chl={$qrData}";
        @endphp

        <div class="bg-white d-inline-block p-3 rounded">
            <img src="{{ $qrUrl }}" alt="QR Code" width="250" height="250">
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
