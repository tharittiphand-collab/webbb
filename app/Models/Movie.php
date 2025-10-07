<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'genre', 'duration', 'description', 'poster'
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
}