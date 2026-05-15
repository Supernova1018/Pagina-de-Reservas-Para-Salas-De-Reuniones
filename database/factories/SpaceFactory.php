<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpaceFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Sala Innovación',
            'Sala Ejecutiva A',
            'Sala Boardroom',
            'Sala Creativa',
            'Sala de Capacitación',
            'Sala Multiusos',
            'Sala de Videoconferencia',
        ];

        $name = $this->faker->unique()->randomElement($names);

        return [
            'name'          => $name,
            'slug'          => Str::slug($name),
            'type'          => $this->faker->randomElement(['university', 'corporate']),
            'capacity'      => $this->faker->numberBetween(4, 50),
            'description'   => $this->faker->paragraph(3),
            'rules'         => $this->faker->paragraph(2),
            'price_per_hour'=> $this->faker->randomFloat(2, 0, 150),
            'is_active'     => true,
            'location'      => 'Piso ' . $this->faker->numberBetween(1, 8) . ', Edificio ' . $this->faker->randomElement(['A', 'B', 'C']),
        ];
    }
}