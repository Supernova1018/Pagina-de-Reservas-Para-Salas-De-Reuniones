<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user for Jetstream panel
        User::updateOrCreate(
            ['email' => 'admin@reservas.com'],
            [
                'name'     => 'Administrador',
                'email'    => 'admin@reservas.com',
                'password' => Hash::make('password'),
            ]
        );

        $this->call([
            SpaceSeeder::class,
            AdditionalSpacesSeeder::class,
        ]);
    }
}