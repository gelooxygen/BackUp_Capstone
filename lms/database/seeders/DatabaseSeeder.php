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

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role_name' => \App\Models\User::ROLE_ADMIN,
            'password' => bcrypt('password'),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            'role_name' => \App\Models\User::ROLE_TEACHER,
            'password' => bcrypt('password'),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'role_name' => \App\Models\User::ROLE_STUDENT,
            'password' => bcrypt('password'),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'role_name' => \App\Models\User::ROLE_PARENT,
            'password' => bcrypt('password'),
        ]);
    }
}
