<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = session('user');
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        return $next($request);
    }
}