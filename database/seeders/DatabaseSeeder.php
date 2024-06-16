<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory(10)->create();

        Ticket::factory(100)->recycle($user)->create();

        \App\Models\User::create([
            'name' => 'The Manager',
            'email' => 'manager@manager.com',
            'password' => bcrypt('password'),
            'is_manager' => true
        ]);
        
    }
}
