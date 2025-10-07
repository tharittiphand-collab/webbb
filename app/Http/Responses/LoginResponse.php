<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;



class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // ถ้าเป็นแอดมิน ให้ไปหน้า admin dashboard
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.movies');
        }

        // ถ้าเป็นผู้ใช้ทั่วไป → ไปหน้าแรก
        return redirect()->route('home');
    }
}
