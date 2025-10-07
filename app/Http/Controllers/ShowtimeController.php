<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class ShowtimeController extends Controller
{
    public function getBookedSeats($showtimeId)
    {
        try {
            $bookedSeats = Booking::where('showtime_id', $showtimeId)
                ->where('status', '!=', 'Cancelled')
                ->pluck('seat_number');

            return response()->json($bookedSeats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'ไม่สามารถโหลดข้อมูลที่นั่งได้'], 500);
        }
    }
}