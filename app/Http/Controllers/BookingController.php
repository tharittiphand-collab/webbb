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
    $showtimes = \App\Models\Showtime::where('movie_id', $movieId)->get();
    $theatres = \App\Models\Theatre::all(); 

    // ดึงที่นั่งที่ถูกจองแล้ว (optional)
    $bookedSeats = \App\Models\Booking::where('movie_id', $movieId)
        ->where('status', '!=', 'Cancelled')
        ->pluck('seat_number')
        ->toArray();

    return view('user.booking_seat', compact('movie', 'showtimes', 'theatres', 'bookedSeats'));
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
        'theatre_id' => 'required|exists:theatres,id',
        'showtime_id' => 'required|exists:showtimes,id',
        'seat_numbers' => 'required|string',
    ]);

    $movie = \App\Models\Movie::findOrFail($movieId);
    $seatNumbers = explode(',', $request->seat_numbers);

    // ✅ ดึงข้อมูลที่จองแล้ว
    $alreadyBooked = \App\Models\Booking::where('showtime_id', $request->showtime_id)
        ->whereIn('seat_number', $seatNumbers)
        ->where('status', '!=', 'Cancelled')
        ->pluck('seat_number')
        ->toArray();

    if (count($alreadyBooked) > 0) {
        return back()->withErrors([
            'seat_numbers' => '❌ ที่นั่งเหล่านี้ถูกจองไปแล้ว: ' . implode(', ', $alreadyBooked)
        ]);
    }

    // ✅ ฟังก์ชันคำนวณราคาตามประเภทของที่นั่ง
    $getPriceBySeat = function ($seat) {
        $row = substr($seat, 0, 1); // ตัวอักษรแถว เช่น A, B, C...
        if (in_array($row, ['A', 'B'])) return 119.00;  // Honeymoon
        if (in_array($row, ['F', 'G', 'H'])) return 400.00; // Opera
        return 99.00; // Normal
    };

    
    $totalPrice = 0;
    foreach ($seatNumbers as $seat) {
        $totalPrice += $getPriceBySeat($seat);
    }

    
    $booking = \App\Models\Booking::create([
        'user_id'     => auth()->id(),
        'movie_id'    => $movie->id,
        'theatre_id'  => $request->theatre_id,
        'showtime_id' => $request->showtime_id,
        'seat_number' => implode(',', $seatNumbers), // รวมเป็นสตริงเดียว เช่น "A1,A2,A3"
        'status'      => 'Pending',
        'amount'      => $totalPrice, 
    ]);

    return redirect()->route('booking.history')
        ->with('success', '✅ Booking successful! Total: ' . number_format($totalPrice, 2) . ' ฿');
}

    

    
    public function payment($bookingId)
{
    // Ensure Omise SDK is loaded
    if (!class_exists('OmiseCharge')) {
        require_once base_path('vendor/omise/omise-php/lib/Omise.php');
    }
    // ดึง booking
    $booking = Booking::with(['movie', 'showtime'])->findOrFail($bookingId);

    $amount = (int) ($booking->amount * 100); // Omise ใช้หน่วยสตางค์
    $currency = 'THB';

    $charge = \OmiseCharge::create([
        'amount' => $amount,
        'currency' => $currency,
        'source' => [
            'type' => 'promptpay',
        ],
        'return_uri' => url()->current(),
        'description' => 'Booking ID: ' . $booking->id,
    ], env('OMISE_PUBLIC_KEY'), env('OMISE_SECRET_KEY'));

    $qrImageUrl = $charge['source']['scannable_code']['image']['download_uri'] ?? null;

    return view('user.payment', [
        'booking'      => $booking,
        'qrImageUrl'   => $qrImageUrl,
    ]);
}

    
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
        ->with(['movie', 'showtime.theatre']) 
        ->latest()
        ->get();

    return view('user.booking_history', compact('bookings'));
}
}