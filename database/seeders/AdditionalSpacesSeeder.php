<?php

namespace Database\Seeders;

use App\Models\Space;
use Illuminate\Database\Seeder;

class AdditionalSpacesSeeder extends Seeder
{
    public function run(): void
    {
        $spaces = [
            [
                'name' => 'Sala Focus 1',
                'slug' => 'sala-focus-1',
                'type' => 'meeting',
                'capacity' => 6,
                'description' => 'Pequeña sala para reuniones de equipo y llamadas importantes.',
                'rules' => "• Máximo 6 personas.\n• Mantener silencio en llamadas.",
                'price_per_hour' => 20000,
                'is_active' => true,
                'location' => 'Piso 2, Ala Sur',
                'image' => '/images/spaces/espacio-2.jfif',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '17:00'],
                ],
            ],
            [
                'name' => 'Sala Focus 2',
                'slug' => 'sala-focus-2',
                'type' => 'meeting',
                'capacity' => 6,
                'description' => 'Sala contigua ideal para entrevistas y reuniones cortas.',
                'rules' => "• Máximo 6 personas.\n• Reserva mínima 30 minutos.",
                'price_per_hour' => 20000,
                'is_active' => true,
                'location' => 'Piso 2, Ala Sur',
                'image' => '/images/spaces/salas-reuniones-decoracion-oficinas-11.jpg',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '17:00'],
                ],
            ],
            [
                'name' => 'Auditorio Principal',
                'slug' => 'auditorio-principal',
                'type' => 'auditorium',
                'capacity' => 120,
                'description' => 'Gran auditorio para conferencias y eventos masivos.',
                'rules' => "• Uso sujeto a autorización.\n• Capacidad máxima 120 personas.",
                'price_per_hour' => 150000,
                'is_active' => true,
                'location' => 'Edificio Central, Planta Baja',
                'image' => '/images/spaces/space-7.webp',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '22:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '22:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '22:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '22:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '20:00'],
                    ['day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '16:00'],
                ],
            ],
            [
                'name' => 'Sala Taller Creativo',
                'slug' => 'sala-taller-creativo',
                'type' => 'studio',
                'capacity' => 18,
                'description' => 'Espacio con mesas grandes y material para talleres prácticos.',
                'rules' => "• Mantener orden y limpieza.\n• No se permiten fuegos o llamas abiertas.",
                'price_per_hour' => 40000,
                'is_active' => true,
                'location' => 'Piso 4, Zona Creativa',
                'image' => '/images/spaces/space-8.jfif',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '18:00'],
                    ['day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '18:00'],
                    ['day_of_week' => 3, 'start_time' => '09:00', 'end_time' => '18:00'],
                    ['day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '18:00'],
                    ['day_of_week' => 5, 'start_time' => '09:00', 'end_time' => '16:00'],
                ],
            ],
            [
                'name' => 'Sala de Entrevistas',
                'slug' => 'sala-entrevistas',
                'type' => 'meeting',
                'capacity' => 4,
                'description' => 'Sala pequeña ideal para entrevistas individuales.',
                'rules' => "• Máximo 4 personas.\n• Reserva mínima 30 minutos.",
                'price_per_hour' => 15000,
                'is_active' => true,
                'location' => 'Piso 1, Oficina de RRHH',
                'image' => '/images/spaces/space-9.jfif',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '17:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '17:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '17:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '17:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '16:00'],
                ],
            ],
            [
                'name' => 'Sala Panorámica',
                'slug' => 'sala-panoramica',
                'type' => 'corporate',
                'capacity' => 16,
                'description' => 'Sala de juntas con vista amplia para reuniones ejecutivas y presentaciones.',
                'rules' => "• Máximo 16 personas.\n• Cuidar pantallas y mobiliario.\n• Reserva mínima 4 horas.",
                'price_per_hour' => 60000,
                'is_active' => true,
                'location' => 'Piso 8, Ala Norte',
                'image' => '/images/spaces/space-10.jfif',
                'availabilities' => [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '19:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '19:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '19:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '19:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '18:00'],
                ],
            ],
        ];

        foreach ($spaces as $spaceData) {
            $availabilities = $spaceData['availabilities'];
            unset($spaceData['availabilities']);

            $space = Space::updateOrCreate(['slug' => $spaceData['slug']], $spaceData);

            $space->availabilities()->delete();
            foreach ($availabilities as $avail) {
                $space->availabilities()->create($avail);
            }
        }
    }
}
