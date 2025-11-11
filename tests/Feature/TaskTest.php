<?php

namespace Tests\Feature;

use App\Events\TaskCompleted;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can show a page listing tasks
     */
    public function test_can_show_page_for_list_of_tasks(): void
    {
        $response = $this->get(route('tasks'));

        $response->assertStatus(200);
    }

    /**
     *  Test can get a list of user tasks
     */
    public function test_can_get_list_of_user_tasks()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        for ($i = 1; $i < 4; $i++) {
            Task::create([
                'name' => fake()->name,
                'description' => fake()->text,
                'user_id' => $user->id
            ]);
        }

        $response = $this->get(route('get-user-tasks', $user->id));

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    /**
     *  Test can admin get a list of all tasks
     */
    public function test_can_admin_get_list_of_all_tasks()
    {
        for ($i = 1; $i < 6; $i++) {
            User::factory()->create();
        }

        for ($i = 1; $i < 6; $i++) {

            Task::create([
                'name' => fake()->name,
                'description' => fake()->text,
                'user_id' => rand(1,5)
            ]);
        }

        $admin_role = Role::create([
            'name' => 'admin',
            'description' => 'This is an admin role'
        ]);

        $add_permission = Permission::create([
            'name' => 'add_task',
            'description' => 'This is an add tasks permission'
        ]);

        $edit_permission = Permission::create([
            'name' => 'edit_task',
            'description' => 'This is an edit tasks permission'
        ]);

        $delete_permission = Permission::create([
            'name' => 'delete_task',
            'description' => 'This is a delete tasks permission'
        ]);

        $admin_role->permissions()->attach([
            $add_permission->id,
            $edit_permission->id,
            $delete_permission->id,
        ]);

        $admin = User::factory()->create();

        $admin->roles()->attach([$admin_role->id]);

        $this->actingAs($admin);

        $response = $this->get(route('get-tasks'));

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    /**
     *  Test if show tasks page displays
     */
    public function test_can_show_single_task_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->title,
            'description' => fake()->text,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('show-task', $task->id));

        $response->assertStatus(200);
    }

    /**
     *  Test that can get a single tasks
     */
    public function test_can_get_a_single_task()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->title,
            'description' => fake()->text,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('get-task', $task->id));

        $response->assertStatus(200)
        ->assertJsonIsObject();
    }

    /**
     * Test that unauthenticated users cannot view tasks
     */
    public function test_guest_cannot_view_tasks()
    {
        $response = $this->get(route('get-tasks'));

        $response->assertStatus(403);
    }

    /**
     * Test that a form to create a tasks displays
     */
    public function test_can_show_create_task_form()
    {
        $response = $this->get(route('task-add-form'));
        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can add a tasks
     */
    public function test_can_add_a_task(){

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('save-task'),[
            'name' => fake()->name(),
            'description' => fake()->text(),
            'user_id' => $user->id
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'tasks' => ['id', 'name', 'description', 'user_id']
            ]);

        // verify the tasks was actually created in the database
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
        ]);
    }
    /**
     *  Test that unauthenticated users cannot create tasks
     */
    public function test_guest_cannot_create_task()
    {
        $response = $this->post(route('save-task'),[
            'name' => fake()->name,
            'description' => fake()->text,
            'user_id' => 1
        ]);

        $response->assertStatus(302);
    }

    /**
     *  Test that an edit form is displayed
     */
    public function test_can_show_edit_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
           'name' => fake()->name,
           'description' => fake()->text,
           'user_id' => $user->id
        ]);

        $response = $this->get(route('edit-task-form', $task));

        $response->assertStatus(200);
    }

    /**
     * Test that a tasks can be updated
     */
    public function test_can_update_task()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
           'name' => fake()->jobTitle,
           'description' => fake()->text,
           'user_id' => $user->id
        ]);

        $new_name = fake()->jobTitle;
        $new_description = 'New '.fake()->text;

        $response = $this->putJson(route('update-task', $task),[
            'name' => $new_name,
            'description' => $new_description
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message','tasks'
            ]);

        //verify the tasks was actually updated in the db
        $this->assertDatabaseHas(
            'tasks',[
                'id' => $task->id,
                'name' => $new_name,
                'description' => $new_description,
                'user_id' =>$user->id
            ]
        );
    }

    /**
     * Test that can dispatch email when the tasks status is updated to completed
     */
    public function test_can_dispatch_email_when_status_is_updated_to_completed()
    {
        Event::fake([TaskCompleted::class]);

        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->jobTitle,
            'description' => fake()->text,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $new_status  = 'completed';

        $response = $this->putJson(route('update-task',$task),[
            'status' => $new_status,
            'description' => $task->description,
            'name' => $task->name,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'tasks', 'message'
            ]);

       // assert the event was dispatched
        Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });

        // verify database has a tasks
        $this->assertDatabaseHas('tasks',[
            'id' => $task->id,
            'name' => $task->name,
            'status' => 'completed',
            'user_id' =>$user->id
        ]);

    }
    /**
     * Test that event is NOT dispatched when the tasks status is NOT completed
     */
    public function test_event_is_not_dispatched_when_task_status_is_not_completed()
    {
        Event::fake([TaskCompleted::class]);

        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->jobTitle,
            'description' => fake()->text,
            'user_id' => $user->id,
        ]);

        $response =  $this->putJson(route('update-task', $task),[
            'status' => 'in_progress',
            'description' => $task->description,
            'name' => $task->name,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['tasks', 'message']);

        // Assert that event was not dispatched
        Event::assertNotDispatched(TaskCompleted::class);

    }

    /**
     * Test that a user can not update other user tasks
     */
    public function test_user_cannot_update_other_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $task = Task::create([
            'name' => fake()->jobTitle,
            'description' => fake()->text,
            'user_id' => $user2->id,
        ]);

        $response = $this->putJson(route('update-task', $task), [
            'name' => 'New Name',
            'description' => 'New Description'
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'You can\'t update tasks.']);

    }

    /**
     * Test if tasks can be deleted
     */
    public function test_can_delete_a_task()
    {

        $user = User::factory()->create();

        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->title,
            'description' => fake()->text,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('delete-task', $task->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        // Since Task uses SoftDeletes, check that it's soft deleted
        $this->assertSoftDeleted('tasks',[
            'id' => $task->id
        ]);

        // verify deleted_at is not null
        $this->assertDatabaseHas('tasks',[
            'id' => $task->id,
        ]);

        $deleted_task = Task::withTrashed()->find($task->id);
        $this->assertNotNull($deleted_task->deleted_at);
    }

    /**
     * Test that a user can not delete other user tasks
     */
    public function test_user_cannot_delete_other_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $task = Task::create([
            'name' => fake()->jobTitle,
            'description' => fake()->text,
            'user_id' => $user2->id,
        ]);

        $response = $this->delete(route('delete-task', $task->id));

        $response->assertStatus(403);

    }


    /**
     *  Test if tasks can be restored
     */
    public function test_can_restore_deleted_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->title,
            'description' => fake()->text,
            'user_id' => $user->id
        ]);

        // soft delete
        $task->delete();

        // verify tasks has been deleted
        $this->assertSoftDeleted('tasks',[
            'id' => $task->id
        ]);

        // restore the tasks
        $response = $this->put(route('restore-task', $task->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        // verify its restored
        $this->assertNotSoftDeleted('tasks',[
            'id' => $task->id
        ]);

        $restored_task = Task::find($task->id);
        $this->assertNull($restored_task->deleted_at);
    }

    /**
     *  Test if tasks can be permanently deleted
     */
    public function test_can_task_be_deleted_permanently()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::create([
            'name' => fake()->title,
            'description' => fake()->text,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('delete-task-permanently', $task->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    /**
     *  test that tasks name is required
     */
    public function test_task_name_is_required()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('save-task'),[
            'user_id' => $user->id,
            'description' => fake()->text()
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that tasks description is required
     */
    public function test_task_description_is_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('save-task'), [
            'name' => fake()->name(),
            'user_id' => $user->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }
}
