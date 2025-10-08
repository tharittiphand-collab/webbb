<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Theatre;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
{
    $movieCount = \App\Models\Movie::count();
    $showtimeCount = \App\Models\Showtime::count();
    $theatreCount = \App\Models\Theatre::count();

    return view('admin.dashboard', compact('movieCount', 'showtimeCount', 'theatreCount'));
}

    /**
     * ✅ แสดงหน้าเดียวรวมทุกอย่าง (หนัง / โรง / เวลา)
     */
    public function movies()
    {
        $movies = Movie::latest()->get();
        $theatres = Theatre::all();
        $showtimes = Showtime::with(['movie', 'theatre'])->latest()->get();

        return view('admin.movies', compact('movies', 'theatres', 'showtimes'));
    }

    /**
     * ✅ เพิ่มหนังใหม่
     */
    public function storeMovie(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $posterPath = $request->hasFile('poster')
            ? $request->file('poster')->store('posters', 'public')
            : null;

        Movie::create([
            'title' => $request->title,
            'genre' => $request->genre,
            'description' => $request->description,
            'poster' => $posterPath,
        ]);

        return back()->with('success', '🎬 Movie added successfully!');
    }

    public function destroyMovie($id)
    {
        $movie = Movie::findOrFail($id);

        if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
            Storage::disk('public')->delete($movie->poster);
        }

        $movie->delete();
        return back()->with('success', '🗑️ Movie deleted!');
    }

    /**
     * ✅ แสดงหน้าแก้ไขหนัง
     */
    public function editMovie($id)
    {
        $movie = Movie::findOrFail($id);
        $theatres = Theatre::all();
        $showtimes = Showtime::where('movie_id', $id)->with('theatre')->get();

        return view('admin.edit-movie', compact('movie', 'theatres', 'showtimes'));
    }

    /**
     * ✅ อัปเดตข้อมูลหนัง
     */
    public function updateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'genre' => $request->genre,
            'description' => $request->description,
        ];

        // อัปเดตโปสเตอร์ถ้ามีการอัปโลดใหม่
        if ($request->hasFile('poster')) {
            // ลบรูปเก่า
            if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
                Storage::disk('public')->delete($movie->poster);
            }
            // อัปโลดรูปใหม่
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $movie->update($data);

        return back()->with('success', '✅ Movie updated successfully!');
    }

    /**
     
     */
    public function storeTheatre(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:theatres,name',
        ]);

        Theatre::create(['name' => $request->name]);
        return back()->with('success', '🏠 Theatre added!');
    }

    public function destroyTheatre($id)
    {
        Theatre::findOrFail($id)->delete();
        return back()->with('success', '🏠 Theatre deleted!');
    }

    /**
     
     */
    public function storeShowtime(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'theatre_id' => 'required|exists:theatres,id',
            'show_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Showtime::create($request->all());
        return back()->with('success', '🕒 Showtime added!');
    }

    public function destroyShowtime($id)
    {
        Showtime::findOrFail($id)->delete();
        return back()->with('success', '🕒 Showtime deleted!');
    }
}
