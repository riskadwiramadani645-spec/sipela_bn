<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class HandleDatabaseErrors
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } catch (QueryException $e) {
            \Log::error('Database Error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan database'], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data');
        }
    }
}