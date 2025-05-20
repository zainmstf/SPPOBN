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
        // Data admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama_lengkap' => 'Administrator Sistem',
            'email' => 'admin@osteoporosis.app',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Sistem Pakar No. 1, Kota Bandung',
            'role' => 'admin'
        ]);

        // Data user biasa
        $users = [
            [
                'username' => 'user1',
                'password' => Hash::make('user123'),
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'no_telepon' => '081234567891',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'role' => 'user'
            ],
            [
                'username' => 'user2',
                'password' => Hash::make('user123'),
                'nama_lengkap' => 'Ani Wijaya',
                'email' => 'ani@example.com',
                'no_telepon' => '081234567892',
                'alamat' => 'Jl. Sudirman No. 25, Surabaya',
                'role' => 'user'
            ],
            [
                'username' => 'lansia1',
                'password' => Hash::make('lansia123'),
                'nama_lengkap' => 'Siti Rahayu',
                'email' => 'siti@example.com',
                'no_telepon' => '081234567893',
                'alamat' => 'Jl. Pahlawan No. 5, Yogyakarta',
                'role' => 'user'
            ],
            [
                'username' => 'lansia2',
                'password' => Hash::make('lansia123'),
                'nama_lengkap' => 'Rudi Hermawan',
                'email' => 'rudi@example.com',
                'no_telepon' => '081234567894',
                'alamat' => 'Jl. Gatot Subroto No. 15, Semarang',
                'role' => 'user'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Seeder user berhasil dijalankan!');
        $this->command->info('Admin: username=admin, password=admin123');
        $this->command->info('User biasa: username=user1/user2/lansia1/lansia2, password=user123/lansia123');
    }
}