<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::create([
            'name' => "Admin Travel Travique",
            'username' => 'admintravel01',
            'email' => 'admintravel@gmail.com',
            'password' => 'passwordadmin123',
            'role' => 'admin',
        ]);
    }
}
