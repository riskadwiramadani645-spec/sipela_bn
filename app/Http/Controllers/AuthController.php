<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            // Cari user berdasarkan username saja
            $user = User::where('username', $credentials['username'])
                       ->where('is_active', true)
                       ->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Update last login
                $user->update(['last_login' => now()]);
                
                // Regenerate session untuk keamanan
                $request->session()->regenerate();
                
                // Simpan user ke session
                $request->session()->put('user', $user);
                
                // Debug log
                \Log::info('User login successful', ['user_id' => $user->user_id, 'level' => $user->level, 'username' => $user->username, 'siswa_id' => $user->siswa_id]);
                
                // Redirect berdasarkan level user dari database
                $redirectRoute = $this->getRedirectRoute($user->level);
                \Log::info('Redirect route', ['route' => $redirectRoute]);
                
                return redirect()->route($redirectRoute)->with('success', 'Login berhasil! Selamat datang.');
            }

            return back()->withErrors([
                'login' => 'Username atau password salah, atau akun tidak aktif.',
            ])->onlyInput('username');
            
        } catch (\Exception $e) {
            // Clear session jika ada error
            $request->session()->flush();
            $request->session()->regenerate();
            
            // Log error untuk debugging
            \Log::error('Login error: ' . $e->getMessage());
            
            return back()->withErrors([
                'login' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ])->onlyInput('username');
        }
    }

    private function getRedirectRoute($level)
    {
        switch ($level) {
            case 'admin':
                return 'admin.dashboard';
            case 'kesiswaan':
                return 'kesiswaan.dashboard';
            case 'guru':
                return 'guru.dashboard';
            case 'wali_kelas':
                return 'guru.dashboard'; // Wali kelas juga ke guru dashboard
            case 'konselor_bk':
                return 'konselor-bk.dashboard';
            case 'kepala_sekolah':
                return 'kepala-sekolah.dashboard';
            case 'siswa':
                return 'siswa.dashboard';
            case 'orang_tua':
                return 'orang-tua.dashboard';
            default:
                return 'login';
        }
    }



    public function logout(Request $request)
    {
        try {
            // Hapus user dari session
            $request->session()->forget('user');
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Exception $e) {
            // Jika ada error dengan session, tetap lanjutkan logout
        }
        
        // Redirect ke login universal
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}