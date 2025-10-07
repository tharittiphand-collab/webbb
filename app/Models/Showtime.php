<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'theatre_id', 'show_date', 'start_time', 'end_time'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function theatre()
    {
        return $this->belongsTo(Theatre::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
