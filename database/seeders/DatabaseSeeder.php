<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);
    }
}

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

        $this->call(RoleSeeder::class); // Add this line to run RoleSeeder first

        \App\Models\User::firstOrCreate([
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'name' => 'Default Admin',
            'password' => bcrypt('admin1234'),
            'role_id' => Role::where('name', 'admin')->first()->id,
            'must_change_password' => false,
        ]);
    }
}
