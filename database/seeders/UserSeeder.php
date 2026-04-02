<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Jorge',
            'email' => 'jorge@gmail.com',
            'password' => '123456'
        ]);

        User::query()->create([
            'name' => 'Marya',
            'email' => 'marya@gmail.com',
            'password' => '123123'
        ]);
    }
}