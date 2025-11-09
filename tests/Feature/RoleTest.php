<?php

namespace Tests\Feature;


use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated user cannot access roles
     */
    public function test_user_can_save_roles(): void
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('add-role'),[
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('roles',[
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);
    }

    /**
     * Test a user can fetch a single role
     */
    public function test_user_can_fetch_a_single_role()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $response = $this->get(route('get-role', $role->id));

        $response->assertStatus(200)
            ->assertJsonIsObject();
    }

    /**
     *  Test a user can fetch a list of role
     */
    public function test_user_can_fetch_a_list_of_Role()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $response = $this->get(route('get-roles'));

        $response->assertStatus(200)
            ->assertJsonIsArray();

        $this->assertDatabaseCount('roles',2);
    }
}
