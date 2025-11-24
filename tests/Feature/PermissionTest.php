<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;
    /**
     *  Test to display a list of permissions page.
     */
    public function test_can_show_list_of_permissions(): void
    {
        $response = $this->get(route('permissions'));

        $response->assertStatus(200)
            ->assertInertia();
    }

    /**
     * Test to display a list of permissions
     */
    public function test_can_fetch_list_of_permissions(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->getJson(route('get-permissions'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'permissions' => [
                    ['id', 'name']
                ]
            ]);
    }

    /**
     * Test to display create a new permission form
     */
    public function test_can_show_create_permission_form(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->get(route('permission-add-form'));

        $response->assertStatus(200)
            ->assertInertia();
    }

    /**
     * Test that a permission name is required
     */
    public function test_permission_name_is_required(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->postJson(route('add-permission'), [
            'description' => 'This permission allows to create task'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that the permission description is required
     */
    public function test_permission_description_is_required(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->postJson(route('add-permission'), [
            'name' => 'create-task'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    /**
     * Test to create a new permission
     */
    public function test_can_create_new_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->postJson(route('add-permission'), [
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('permissions', [
            'name' => 'create-task'
        ]);
    }

    /**
     *  Test can display a single permission
     */
    public function test_can_display_single_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->get(route('show-permission', $permission->id));

        $response->assertStatus(200)
            ->assertInertia();
    }

    /**
     * Test to get a single permission
     */
    public function test_can_get_single_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->getJson(route('get-permission', $permission->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['permission']);

        $this->assertEquals($permission->name, $response->json('permission.name'));
    }

    /**
     * Test can display edit permission form
     */
    public function test_can_display_edit_permission_form(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->get(route('edit-permission-form', $permission->id));

        $response->assertStatus(200)
            ->assertInertia();
    }

    /**
     * Test can update a permission
     */
    public function test_can_update_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $new_permission_name = 'edit-task';

        $new_permission_description = 'This permission allows to edit task';

        $response = $this->putJson(route('update-permission', $permission->id), [
            'name' => $new_permission_name,
            'description' => $new_permission_description
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'permission']);


        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => $new_permission_name,
            'description' => $new_permission_description
        ]);
    }

    /**
     *  Test can soft delete a permission
     */
    public function test_can_soft_delete_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->deleteJson(route('delete-permission', $permission->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertSoftDeleted('permissions', [
            'id' => $permission->id
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id
        ]);

        $deleted_permission = Permission::withTrashed()->find($permission->id);

        $this->assertNotNull($deleted_permission->deleted_at);

    }

    /**
     * Test can recover a deleted permission
     */
    public function test_can_recover_deleted_permission(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $permission->delete();

        $response = $this->put(route('restore-permission', $permission->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertNotSoftDeleted('permissions', [
            'id' => $permission->id
        ]);

        $retored_permission = Permission::find($permission->id);

        $this->assertNull($retored_permission->deleted_at);

    }

    /**
     * Test can delete a permission permanently
     */
    public function test_can_delete_permission_permanently(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This permission allows to create task'
        ]);

        $response = $this->delete(route('delete-permission-permanently', $permission->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id
        ]);
    }

}
