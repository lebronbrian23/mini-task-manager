<?php

namespace Tests\Feature;


use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;


    /**
     *  Test if a new role form displays
     */
    public function test_can_add_role_form_display()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('role-add-form'));

        $response->assertStatus(200)
            ->assertInertia();
    }
    /**
     *  Test if a user can save a role
     * @return void
     */
    public function test_user_can_save_a_role(): void
    {

        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

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
     * Test if a page to assign permission to role can be displayed
     */
    public function test_can_assign_permission_to_role_page_display()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->get(route('form-attach-permission-to-role', $user->id));

        $response->assertStatus(200)
            ->assertInertia();

    }

    /**
     * Test can attach a permission to a role
     */
    public function test_can_attach_permission_to_role()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $new_role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This is a create task permission'
        ]);

        $response = $this->postJson(route('attach-permission-to-role', $new_role->id), [
            'permission_ids' => [$permission->id]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('permission_role', [
            'role_id' => $new_role->id,
            'permission_id' => $permission->id
        ]);

    }

    /**
     * Test can dettach a permission from a role
     */
    public function test_can_dettach_permission_from_role()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $new_role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $permission = Permission::create([
            'name' => 'create-task',
            'description' => 'This is a create task permission'
        ]);

        $new_role->permissions()->attach([$permission->id]);

        $response = $this->deleteJson(route('detach-permission-from-role'),[
            'role_id' => $new_role->id,
            'permission_ids' => [$permission->id]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('permission_role', [
            'role_id' => $new_role->id,
            'permission_id' => $permission->id
        ]);

    }

    /**
     *  Test that a role name has been provided
     */
    public function test_a_role_name_has_been_provided()
    {
        $admin_role = Role::create([
           'name' => 'admin',
           'description' => 'This is an admin.'
        ]);
        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $this->actingAs($user);

        $response = $this->postJson(route('add-role'), [
            'description' => 'This is a customer.'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     *  Test that a role description has been provided
     */
    public function test_a_role_description_has_been_provided()
    {
        $admin_role = Role::create([
           'name' => 'admin',
           'description' => 'This is an admin.'
        ]);
        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $this->actingAs($user);

        $response = $this->postJson(route('add-role'), [
            'name' => 'Customer.'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    /**
     *  Test if a page to display a single role displays
     */
    public function test_user_can_view_page_to_display_single_role()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $response = $this->get(route('show-role', $role->id));

        $response->assertStatus(200)
            ->assertInertia();
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
     * Test if a page listing all roles displays
     */
    public function test_can_list_roles_page_displays()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('roles'));

        $response->assertStatus(200)
        ->assertInertia();
    }
    /**
     *  Test a user can fetch a list of roles
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

    /**
     *  Test if edit role page displays
     */
    public function test_can_edit_role_page_display()
    {
        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'editor',
            'description' => 'This is an editor'
        ]);

        $response = $this->get(route('edit-role', $role));

        $response->assertStatus(200)
            ->assertInertia();
    }

    /**
     *  Test if a role can be updated
     */

    /**
     *  Test if a role can be updated
     */
    public function test_can_role_be_updated()
    {

        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        // Refresh the user to load the relationship
        $user->refresh();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $new_name = 'Editor.';

        $new_description = 'This is an editor.';

        $response = $this->putJson(route('update-role', $role), [
            'description' => $new_description,
            'name' => $new_name
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'role']);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $new_name,
            'description' => $new_description
        ]);

    }

    /**
     * Test if a role can be deleted
     */
    public function test_can_role_be_deleted()
    {
        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $user->refresh();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'admined',
            'description' => 'This is an admined'
        ]);

        $response = $this->delete(route('delete-role', $role->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertSoftDeleted('roles',[
            'id' => $role->id
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id
        ]);

        $deleted_role = Role::withTrashed()->find($role->id);

        $this->assertNotNull($deleted_role->deleted_at);
    }

    /**
     *  Test a soft deleted role can be restored
     */
    public function test_can_a_soft_deleted_role_be_restored()
    {

        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $user->refresh();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'Guest',
            'description' => 'This is a guest.'
        ]);

        $role->delete();

        $this->assertSoftDeleted('roles',[
            'id' => $role->id
        ]);

        $response = $this->put(route('restore-role', $role->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertNotSoftDeleted('roles',[
            'id' => $role->id
        ]);

        $restored_role = Role::find($role->id);
        $this->assertNull($restored_role->deleted_at);

    }

    /**
     * Test if role can be permanently deleted
     */
    public function test_can_role_be_deleted_permanently()
    {
        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$admin_role->id]);

        $user->refresh();

        $this->actingAs($user);

        $role = Role::create([
            'name' => 'Customer',
            'description' => 'This is a customer'
        ]);

        $response = $this->delete(route('delete-role-permanently', $role->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('roles',[
            'id' => $role->id
        ]);

    }

    /**
     * Test if a page to assign role to a user can be displayed
     */
    public function test_can_assign_role_to_user_page_display()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $response = $this->get(route('form-assign-role-to-user', $user->id));

        $response->assertStatus(200)
            ->assertInertia();

    }

    /**
     * Test if a role can be assigned to a user
     */
    public function test_can_role_be_assigned_to_user()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $new_role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $new_user = User::factory()->create();

        $response = $this->postJson(route('assign-role-to-user', $new_user->id), [
            'role_ids' => [$new_role->id]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('role_user', [
            'role_id' => $new_role->id,
            'user_id' => $new_user->id
        ]);
    }

    /**
     * Test if a role can be detached from a user
     */
    public function test_can_role_be_detached_from_user()
    {
        $role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin'
        ]);

        $user = User::factory()->create();

        $user->roles()->attach([$role->id]);

        $user->refresh();

        $this->actingAs($user);

        $new_role = Role::create([
            'name' => 'customer',
            'description' => 'This is a customer'
        ]);

        $new_user = User::factory()->create();

        $new_user->roles()->attach([$new_role->id]);

        $response = $this->deleteJson(route('remove-role-from-user', $new_user->id), [
            'role_ids' => [$new_role->id]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('role_user', [
            'role_id' => $new_role->id,
            'user_id' => $new_user->id
        ]);

    }


}
