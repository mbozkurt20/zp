<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin rolünü oluştur
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // 2. Admin kullanıcıyı oluştur (zaten varsa al)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@emisoft.com.tr'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'), // Güvenlik için daha güçlü şifre kullan
            ]
        );

        // 3. Role ata (zaten atanmışsa tekrar etmez)
        $adminUser->assignRole($adminRole);
    }
}
