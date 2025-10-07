<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theatre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'total_seats'];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}

