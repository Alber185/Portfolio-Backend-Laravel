<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@portfolio.test')],
            [
                'name'     => env('ADMIN_NAME', 'Portfolio Owner'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ]
        );
    }
}
