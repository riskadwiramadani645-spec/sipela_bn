<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AllUsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'level' => 'admin',
                'can_verify' => true,
                'is_active' => true
            ],
            [
                'username' => 'kesiswaan',
                'password' => Hash::make('kesiswaan123'),
                'level' => 'kesiswaan',
                'can_verify' => true,
                'is_active' => true
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }
    }
}