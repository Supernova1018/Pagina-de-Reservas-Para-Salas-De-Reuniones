<?php

namespace Tests\Feature;

use App\Models\Availability;
use App\Models\Reservation;
use App\Models\Space;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_manage_spaces_via_api(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Sala API',
            'slug' => 'sala-api',
            'type' => 'corporate',
            'capacity' => 12,
            'description' => 'Sala creada desde la API.',
            'rules' => 'No comer dentro.',
            'price_per_hour' => 45.50,
            'is_active' => true,
            'location' => 'Piso 3',
        ];

        $createResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/spaces', $payload);

        $createResponse->assertCreated()
            ->assertJsonPath('space.slug', 'sala-api');

        $this->assertDatabaseHas('spaces', [
            'slug' => 'sala-api',
            'name' => 'Sala API',
        ]);

        $updateResponse = $this->actingAs($user, 'sanctum')
            ->patchJson('/api/spaces/sala-api', array_merge($payload, [
                'name' => 'Sala API Actualizada',
                'slug' => 'sala-api-actualizada',
                'capacity' => 20,
                'price_per_hour' => 60,
            ]));

        $updateResponse->assertOk()
            ->assertJsonPath('space.slug', 'sala-api-actualizada');

        $this->assertDatabaseHas('spaces', [
            'slug' => 'sala-api-actualizada',
            'capacity' => 20,
        ]);

        $deleteResponse = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/spaces/sala-api-actualizada');

        $deleteResponse->assertOk()
            ->assertJsonPath('message', 'Sala eliminada correctamente.');

        $this->assertDatabaseMissing('spaces', [
            'slug' => 'sala-api-actualizada',
        ]);
    }

    public function test_authenticated_users_can_manage_reservations_and_overlap_is_blocked(): void
    {
        $user = User::factory()->create();
        $space = Space::factory()->create([
            'slug' => 'sala-reservas',
            'price_per_hour' => 50,
        ]);

        $start = Carbon::now()->addDays(2)->setTime(10, 0, 0);
        $end = $start->copy()->addHour();

        Availability::create([
            'space_id' => $space->id,
            'day_of_week' => $start->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
        ]);

        $createResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reservations', [
                'space_slug' => $space->slug,
                'start_time' => $start->toIso8601String(),
                'end_time' => $end->toIso8601String(),
                'user_name' => 'Juan Pérez',
                'user_email' => 'juan@example.com',
                'user_phone' => '3000000000',
                'organization' => 'Empresa Uno',
                'notes' => 'Reserva inicial.',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('reservation.space.slug', 'sala-reservas');

        $reservation = Reservation::firstOrFail();

        $overlapResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reservations', [
                'space_slug' => $space->slug,
                'start_time' => $start->copy()->addMinutes(30)->toIso8601String(),
                'end_time' => $end->copy()->addMinutes(30)->toIso8601String(),
                'user_name' => 'María López',
                'user_email' => 'maria@example.com',
                'user_phone' => '3000000001',
                'organization' => 'Empresa Dos',
                'notes' => 'Debe fallar por solapamiento.',
            ]);

        $overlapResponse->assertStatus(422)
            ->assertJsonPath('message', 'El horario no está disponible.');

        $newStart = $start->copy()->addHours(2);
        $newEnd = $newStart->copy()->addHour();

        $updateResponse = $this->actingAs($user, 'sanctum')
            ->patchJson('/api/reservations/' . $reservation->slug, [
                'space_slug' => $space->slug,
                'start_time' => $newStart->toIso8601String(),
                'end_time' => $newEnd->toIso8601String(),
                'user_name' => 'Juan Pérez Actualizado',
                'user_email' => 'juan.actualizado@example.com',
                'user_phone' => '3000000099',
                'organization' => 'Empresa Uno',
                'notes' => 'Reserva actualizada.',
                'status' => 'confirmed',
            ]);

        $updateResponse->assertOk()
            ->assertJsonPath('reservation.user_name', 'Juan Pérez Actualizado')
            ->assertJsonPath('reservation.status', 'confirmed');

        $this->assertDatabaseHas('reservations', [
            'slug' => $reservation->slug,
            'status' => 'confirmed',
        ]);

        $deleteResponse = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/reservations/' . $reservation->slug);

        $deleteResponse->assertOk()
            ->assertJsonPath('message', 'Reserva eliminada correctamente.');

        $this->assertDatabaseMissing('reservations', [
            'slug' => $reservation->slug,
        ]);
    }

    public function test_api_login_returns_token_and_me_endpoint_works(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure(['message', 'user', 'token', 'type']);

        $token = $loginResponse->json('token');

        $meResponse = $this->withToken($token)
            ->getJson('/api/me');

        $meResponse->assertOk()
            ->assertJsonPath('user.email', $user->email);
    }
}