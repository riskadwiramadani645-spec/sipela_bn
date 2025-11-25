<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
          'username' => 'admin',
                'password' => Hash::make('admin123'),
                'level' => 'admin',
                'can_verify' => true,
                'created_at' => now()
        ]);
    }
}