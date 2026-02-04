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
        // Create default admin user
        User::create([
            'name' => 'Ahmad Mustofa',
            'email' => 'mustofaahmad@poltera.ac.id',
            'password' => bcrypt('ZXCasd123!@#'),
        ]);

        User::create([
            'name' => 'Demo User',
            'email' => 'demo@wait.icminovasi.my.id',
            'password' => bcrypt('password'),
        ]);

        // Seed conversations
        $this->call(ConversationSeeder::class);
    }
}
