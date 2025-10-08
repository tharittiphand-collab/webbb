<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TheatreController;

/*
|--------------------------------------------------------------------------
| 🌍 Public Routes (ทั่วไป)
|--------------------------------------------------------------------------
*/

// หน้าแรก = แสดงรายการภาพยนตร์ทั้งหมด
Route::get('/', [MovieController::class, 'index'])->name('home');

// หน้ารายละเอียดภาพยนตร์
Route::get('/movie/{id}', [MovieController::class, 'show'])->name('movie.show');


/*
|--------------------------------------------------------------------------
| 🔒 Protected Routes (ต้องล็อกอินก่อน)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ถ้าผู้ใช้ไปที่ /dashboard ให้กลับไปหน้าแรกแทน
    Route::get('/dashboard', fn() => redirect()->route('home'));

    // ✅ ระบบจองที่นั่ง
    Route::get('/booking/{id}', [BookingController::class, 'selectSeat'])->name('booking.seat');
    Route::post('/booking/{id}', [BookingController::class, 'store'])->name('booking.store');

    // ✅ การชำระเงิน
    Route::get('/booking/payment/{bookingId}', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/payment/{bookingId}/update', [BookingController::class, 'updateStatus'])->name('booking.updateStatus');

    // ✅ ประวัติการจองของผู้ใช้
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('booking.history');
});


/*
|--------------------------------------------------------------------------
| 🏛️ Admin Routes (เฉพาะผู้ดูแลระบบ)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {

    // Dashboard (หน้าแรกของแอดมิน)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ✅ จัดการหนัง (All-in-one)
    Route::get('/movies', [AdminController::class, 'movies'])->name('admin.movies');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('admin.movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('admin.movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'destroyMovie'])->name('admin.movies.destroy');

    // ✅ จัดการโรงหนัง
    Route::post('/theatres', [AdminController::class, 'storeTheatre'])->name('admin.theatres.store');
    Route::delete('/theatres/{id}', [AdminController::class, 'destroyTheatre'])->name('admin.theatres.destroy');

    // ✅ จัดการรอบฉาย
    Route::post('/showtimes', [AdminController::class, 'storeShowtime'])->name('admin.showtimes.store');
    Route::delete('/showtimes/{id}', [AdminController::class, 'destroyShowtime'])->name('admin.showtimes.destroy');
});


/*
|--------------------------------------------------------------------------
| ⚙️ AJAX Routes (ใช้กับ JavaScript fetch)
|--------------------------------------------------------------------------
*/
// ✅ ดึงรอบฉายของโรงที่เลือก (ใช้ตอนเลือก Theatre)
Route::get('/api/movies/{movieId}/theatres/{theatreId}/showtimes', [BookingController::class, 'getShowtimesByTheatre']);

// ✅ ดึงที่นั่งที่จองแล้ว (ใช้ตอนเลือก Showtime)
Route::get('/api/showtime/{showtimeId}/booked-seats', [ShowtimeController::class, 'getBookedSeats']);


/*
|--------------------------------------------------------------------------
| 👤 Auth Routes (Register / Login จาก Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

