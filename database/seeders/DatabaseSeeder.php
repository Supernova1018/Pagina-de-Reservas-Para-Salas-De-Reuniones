<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ],
            [
                'name' => 'Coordinador Salas',
                'email' => 'coordinador@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ],
            [
                'name' => 'Ana Ruiz',
                'email' => 'ana@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ],
            [
                'name' => 'Carlos Gómez',
                'email' => 'carlos@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ],
            [
                'name' => 'Laura Torres',
                'email' => 'laura@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ],
            [
                'name' => 'Miguel Pérez',
                'email' => 'miguel@reservas.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->call([
            SpaceSeeder::class,
            AdditionalSpacesSeeder::class,
        ]);
    }
}