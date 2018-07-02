<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        // if (Auth::user()->role == 'SuperAdmin')
        if (Auth::user()->username == 'fen' || Auth::user()->username =='gr')
            return $next($request);
        return redirect('error');
    }
}