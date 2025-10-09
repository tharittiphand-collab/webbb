<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TheatreController;




Route::get('/', [MovieController::class, 'index'])->name('home');


Route::get('/movie/{id}', [MovieController::class, 'show'])->name('movie.show');



Route::middleware(['auth'])->group(function () {

    
    Route::get('/dashboard', fn() => redirect()->route('home'));

    
    Route::get('/booking/{id}', [BookingController::class, 'selectSeat'])->name('booking.seat');
    Route::post('/booking/{id}', [BookingController::class, 'store'])->name('booking.store');

    
    Route::get('/booking/payment/{bookingId}', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/payment/{bookingId}/update', [BookingController::class, 'updateStatus'])->name('booking.updateStatus');

    
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('booking.history');
});



Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {

    
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    
    Route::get('/movies', [AdminController::class, 'movies'])->name('admin.movies');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('admin.movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('admin.movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'destroyMovie'])->name('admin.movies.destroy');

    
    Route::post('/theatres', [AdminController::class, 'storeTheatre'])->name('admin.theatres.store');
    Route::delete('/theatres/{id}', [AdminController::class, 'destroyTheatre'])->name('admin.theatres.destroy');

    
    Route::post('/showtimes', [AdminController::class, 'storeShowtime'])->name('admin.showtimes.store');
    Route::delete('/showtimes/{id}', [AdminController::class, 'destroyShowtime'])->name('admin.showtimes.destroy');
});




Route::get('/api/movies/{movieId}/theatres/{theatreId}/showtimes', [BookingController::class, 'getShowtimesByTheatre']);


Route::get('/api/showtime/{showtimeId}/booked-seats', [ShowtimeController::class, 'getBookedSeats']);



require __DIR__.'/auth.php';

