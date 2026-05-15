<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpaceSeeder extends Seeder
{
    public function run(): void
    {
        $spaces = [
            [
                'name'          => 'Sala Innovación',
                'slug'          => 'sala-innovacion',
                'type'          => 'university',
                'capacity'      => 20,
                'description'   => 'Espacio moderno con tecnología de punta para sesiones creativas, talleres y actividades académicas. Cuenta con pantallas inteligentes, pizarras digitales y mobiliario flexible que se adapta a cualquier dinámica de trabajo o aprendizaje.',
                'rules'         => "• Máximo " . 20 . " personas.\n• No se permite el consumo de alimentos.\n• El equipo audiovisual debe dejarse en su lugar.\n• Reserva mínima: 1 hora.",
                'price_per_hour'=> 0,
                'is_active'     => true,
                'location'      => 'Piso 2, Bloque Académico A — Universidad',
                'image'         => '/images/spaces/space-3.jfif',
                'availabilities'=> [
                    ['day_of_week' => 1, 'start_time' => '07:00', 'end_time' => '20:00'],
                    ['day_of_week' => 2, 'start_time' => '07:00', 'end_time' => '20:00'],
                    ['day_of_week' => 3, 'start_time' => '07:00', 'end_time' => '20:00'],
                    ['day_of_week' => 4, 'start_time' => '07:00', 'end_time' => '20:00'],
                    ['day_of_week' => 5, 'start_time' => '07:00', 'end_time' => '18:00'],
                ],
            ],
            [
                'name'          => 'Sala Ejecutiva Boardroom',
                'slug'          => 'sala-ejecutiva-boardroom',
                'type'          => 'corporate',
                'capacity'      => 12,
                'description'   => 'Sala de reuniones corporativa de alto nivel diseñada para juntas directivas, negociaciones y presentaciones ejecutivas. Equipada con sistema de videoconferencia 4K, iluminación regulable, sistema de audio inalámbrico y servicio de catering opcional.',
                'rules'         => "• Capacidad máxima: 12 personas.\n• Requiere reserva con al menos 24h de anticipación.\n• Dress code: formal.\n• Incluye 30 min de setup previo sin costo adicional.",
                'price_per_hour'=> 85000,
                'is_active'     => true,
                'location'      => 'Piso 10, Torre Empresarial Centro — Empresa',
                'image'         => '/images/spaces/space-4.jfif',
                'availabilities'=> [
                    ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '18:00'],
                    ['day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '17:00'],
                ],
            ],
            [
                'name'          => 'Sala Multiusos Coworking',
                'slug'          => 'sala-multiusos-coworking',
                'type'          => 'corporate',
                'capacity'      => 30,
                'description'   => 'Espacio versátil de coworking ideal para capacitaciones, talleres empresariales, lanzamientos de productos y eventos corporativos de mediana escala. Configuración modular con mesas y sillas apilables, proyector HD, sistema de sonido y conexión Wi-Fi de alta velocidad.',
                'rules'         => "• Aforo máximo: 30 personas.\n• Reserva mínima: 2 horas.\n• El espacio debe quedar en las mismas condiciones.\n• Se permiten alimentos empacados.\n• Cancelación gratuita hasta 12h antes.",
                'price_per_hour'=> 45000,
                'is_active'     => true,
                'location'      => 'Piso 3, Hub de Negocios Risaralda — Empresa',
                'image'         => '/images/spaces/space-5.jfif',
                'availabilities'=> [
                    ['day_of_week' => 1, 'start_time' => '06:00', 'end_time' => '22:00'],
                    ['day_of_week' => 2, 'start_time' => '06:00', 'end_time' => '22:00'],
                    ['day_of_week' => 3, 'start_time' => '06:00', 'end_time' => '22:00'],
                    ['day_of_week' => 4, 'start_time' => '06:00', 'end_time' => '22:00'],
                    ['day_of_week' => 5, 'start_time' => '06:00', 'end_time' => '22:00'],
                    ['day_of_week' => 6, 'start_time' => '08:00', 'end_time' => '16:00'],
                ],
            ],
            [
                'name'          => 'Sala de Capacitación Académica',
                'slug'          => 'sala-capacitacion-academica',
                'type'          => 'university',
                'capacity'      => 40,
                'description'   => 'Aula universitaria equipada para conferencias, charlas magistrales, defensa de proyectos y eventos académicos. Cuenta con sistema de grabación, proyector doble, iluminación controlada y acceso para personas en condición de discapacidad.',
                'rules'         => "• Solo para uso académico o eventos aprobados.\n• Máximo 40 asistentes.\n• El uso del equipo de grabación requiere solicitud previa.\n• Prohibido el consumo de alimentos y bebidas.",
                'price_per_hour'=> 0,
                'is_active'     => true,
                'location'      => 'Piso 1, Edificio Principal — Universidad',
                'image'         => '/images/spaces/space-6.jfif',
                'availabilities'=> [
                    ['day_of_week' => 1, 'start_time' => '07:00', 'end_time' => '21:00'],
                    ['day_of_week' => 2, 'start_time' => '07:00', 'end_time' => '21:00'],
                    ['day_of_week' => 3, 'start_time' => '07:00', 'end_time' => '21:00'],
                    ['day_of_week' => 4, 'start_time' => '07:00', 'end_time' => '21:00'],
                    ['day_of_week' => 5, 'start_time' => '07:00', 'end_time' => '19:00'],
                    ['day_of_week' => 6, 'start_time' => '08:00', 'end_time' => '14:00'],
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