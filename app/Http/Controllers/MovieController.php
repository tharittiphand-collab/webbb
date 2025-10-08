<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Booking;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('created_at', 'desc')->get();

        $recommendations = collect(); // ค่าเริ่มต้นว่าง

        if (auth()->check()) {
            // ดึงการจองล่าสุดของผู้ใช้
            $lastBooking = Booking::where('user_id', auth()->id())
                ->with('movie')
                ->latest()
                ->first();

            if ($lastBooking && $lastBooking->movie && $lastBooking->movie->genre) {
                $genre = $lastBooking->movie->genre;

                // หา 5 เรื่องที่มี genre เดียวกัน แต่ไม่ใช่เรื่องที่ดูล่าสุด
                $recommendations = Movie::where('genre', $genre)
                    ->where('id', '!=', $lastBooking->movie->id)
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
            }
        }

        return view('user.index', compact('movies', 'recommendations'));
    }

    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        $showtimes = $movie->showtimes()->get();

        return view('user.movie_detail', compact('movie', 'showtimes'));
    }
}
