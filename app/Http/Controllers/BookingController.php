<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Http\Request;

class BookingController extends Controller
{
   public function selectSeat($movieId)
{
    $movie = \App\Models\Movie::findOrFail($movieId);
    $theatres = \App\Models\Theatre::all(); // ✅ ดึงโรงหนังทั้งหมด
    $showtimes = []; // ยังไม่เลือกโรงเลยส่งว่างไว้ก่อน

    return view('user.booking_seat', compact('movie', 'theatres', 'showtimes'));
}

public function getShowtimesByTheatre($movieId, $theatreId)
{
    $showtimes = \App\Models\Showtime::where('movie_id', $movieId)
                    ->where('theatre_id', $theatreId)
                    ->get(['id', 'show_date', 'start_time', 'end_time']);

    return response()->json($showtimes);
}


public function getAvailableSeats($showtimeId)
{
    $showtime = Showtime::with('theatre')->findOrFail($showtimeId);

    $bookedSeats = Booking::where('showtime_id', $showtimeId)->pluck('seat_number')->toArray();
    $totalSeats = $showtime->theatre->total_seats;

    $availableSeats = [];
    for ($i = 1; $i <= $totalSeats; $i++) {
        $seatLabel = 'A' . $i;
        if (!in_array($seatLabel, $bookedSeats)) {
            $availableSeats[] = $seatLabel;
        }
    }

    return response()->json([
        'theatre' => $showtime->theatre->name,
        'availableSeats' => $availableSeats
    ]);
}


    public function store(Request $request, $movieId)
{
    $request->validate([
        'showtime_id' => 'required|exists:showtimes,id',
        'seat_numbers' => 'required|string',
    ]);

    $movie = Movie::findOrFail($movieId);

    // แปลงที่นั่งเป็น array
    $seats = array_map('trim', explode(',', $request->seat_numbers));
    $seatType = $request->input('seat_type', 'normal');
    $pricePerSeat = ($seatType === 'honeymoon') ? 119.00 : 99.00;

    // ✅ คำนวณราคารวม
    $totalAmount = count($seats) * $pricePerSeat;

    // ✅ สร้างการจองเดียว
    Booking::create([
        'user_id'     => auth()->id(),
        'movie_id'    => $movie->id,
        'showtime_id' => $request->showtime_id,
        'seat_number' => implode(',', $seats), // เช่น "A1,A2,A3"
        'status'      => 'Pending',
        'amount'      => $totalAmount, // ✅ ราคารวมทั้งหมด
    ]);

    return redirect()->route('booking.history')
        ->with('success', 'Booking successful! Total amount: ' . $totalAmount . '฿');
}

    

    
    public function payment($bookingId)
    {
        $booking = Booking::with(['movie', 'showtime'])->findOrFail($bookingId);

        // อนุญาตเฉพาะเจ้าของการจอง (หรือแอดมิน)
        if ($booking->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        // สร้างข้อความ/ลิงก์สำหรับฝังใน QR (เลือก 1 แบบ)
        // แบบข้อความธรรมดา (อ่านง่าย)
        $qrPayloadText = "CinemaTix Payment\n"
            ."Booking ID: {$booking->id}\n"
            ."Movie: {$booking->movie->title}\n"
            ."Amount: {$booking->amount} THB";

        // แบบลิงก์ (ถ้าต้องการให้สแกนแล้วพาไปยังหน้าใดหน้าหนึ่ง)
        // NOTE: ถ้าทดสอบในเครื่อง ให้ใช้ ngrok หรือ IP ภายในเครือข่ายแทน localhost
        $appUrl = rtrim(config('app.url'), '/'); // ต้องตั้ง APP_URL ใน .env
        $qrPayloadUrl = "{$appUrl}/booking/{$booking->id}/payment"; // ลิงก์เดิมก็ได้

        // ส่งไปที่ view ทั้ง 2 แบบ เผื่อเปลี่ยนใจ
        return view('user.payment', [
            'booking'      => $booking,
            'qrPayloadText'=> $qrPayloadText,
            'qrPayloadUrl' => $qrPayloadUrl,
        ]);
    }

    // ✅ ผู้ใช้กด "I have paid" → อัปเดตสถานะเป็น Paid
    public function updateStatus($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $booking->update(['status' => 'Paid']);

        return redirect()->route('booking.history')->with('success', 'Payment successful! Your booking is now PAID.');
    }

   public function history()
{
    $bookings = Booking::where('user_id', auth()->id())
        ->with(['movie', 'showtime.theatre']) // ✅ ดึง theatre มาด้วย
        ->latest()
        ->get();

    return view('user.booking_history', compact('bookings'));
}
}