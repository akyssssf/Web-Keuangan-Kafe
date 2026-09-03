<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kafe.test'],
            [
                'name' => 'Admin Kafe',
                'password' => Hash::make('kafe12345'),
            ]
        );
    }
}
