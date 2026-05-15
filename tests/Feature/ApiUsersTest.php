<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_list_all_users(): void
    {
        $authUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($authUser, 'sanctum')
            ->getJson('/api/users');

        $response->assertOk();
        $response->assertJsonCount(2, 'users');
        $response->assertJsonFragment([
            'id' => $authUser->id,
            'name' => $authUser->name,
            'email' => $authUser->email,
        ]);
        $response->assertJsonFragment([
            'id' => $otherUser->id,
            'name' => $otherUser->name,
            'email' => $otherUser->email,
        ]);
    }
}