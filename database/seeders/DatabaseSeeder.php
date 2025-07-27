<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::firstOrCreate([
            'email' => 'admin@gmail.com',
            'name' => 'Default Admin',
            'password' => bcrypt('admin1234'),
            'role' => 'admin',
            'must_change_password' => false,
        ]);
    }
}
