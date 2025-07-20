<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
       $this->call([
           AdminUserSeeder::class,
           CategorySeeder::class,
       ]);

        $user = User::factory()->create([
            'name' => 'Yönetici',
            'email' => 'admin@app.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('admin');
    }
}
