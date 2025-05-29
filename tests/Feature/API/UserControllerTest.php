<?php

namespace Tests\Feature\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_returns_all_users_successfully(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->get('/api/users');

        $response->assertOk();
        $response->assertJsonIsArray();
        $response->assertJsonCount(User::count());
        $response->assertExactJsonStructure([['id', 'name', 'email']]);
    }
}
