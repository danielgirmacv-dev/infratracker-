<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function validTaskPayload(array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-05-01',
            'project_name' => 'Project Alpha',
            'task_description' => 'Complete documentation.',
            'amount' => '99.50',
            'start_date' => '2026-04-01',
            'end_date' => '2026-06-01',
            'status' => 'Pending',
            'priority' => 'Medium',
            'progress' => 25,
            'next_action' => 'Review draft',
            'responsible_department' => 'Operations',
            'task_given_to' => 'Employee',
            'remark' => 'Notes here',
        ], $overrides);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Select Your Role');
    }

    public function test_actor_login_with_valid_credentials_authenticates_successfully(): void
    {
        $response = $this->post(route('login'), [
            'actor' => 'Project Manager',
            'password' => 'manager123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('active_actor', 'Project Manager');
    }

    public function test_actor_login_with_invalid_credentials_returns_errors(): void
    {
        $response = $this->post(route('login'), [
            'actor' => 'Project Manager',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
    }

    public function test_actor_logout_clears_session(): void
    {
        // Simulate logged in actor
        session(['active_actor' => 'Employee']);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('active_actor');
    }

    public function test_creating_task_generates_correct_notification(): void
    {
        // Set context to Infra Director
        session(['active_actor' => 'Infra Director']);

        $payload = $this->validTaskPayload([
            'task_given_to' => 'Employee',
            'project_name' => 'Design API',
        ]);

        $response = $this->post(route('tasks.store'), $payload);
        $response->assertRedirect(route('tasks.index'));

        // Check task
        $task = Task::where('project_name', 'Design API')->firstOrFail();
        $this->assertSame('Infra Director', $task->task_given_by);

        // Check notification
        $notification = Notification::where('task_id', $task->item_no)->firstOrFail();
        $this->assertSame('Infra Director', $notification->actor);
        $this->assertSame('Employee', $notification->target_actor);
        $this->assertTrue($notification->read_by_director);
        $this->assertFalse($notification->read_by_employee);
        $this->assertStringContainsString('Infra Director assigned task \'Design API\' to Employee', $notification->message);
    }

    public function test_updating_task_generates_notification_for_all(): void
    {
        $task = Task::create($this->validTaskPayload([
            'project_name' => 'Build frontend',
            'task_given_to' => 'Employee',
            'status' => 'Pending',
            'progress' => 10,
        ]));

        // Set context to Employee
        session(['active_actor' => 'Employee']);

        $updatedPayload = $this->validTaskPayload([
            'project_name' => 'Build frontend',
            'task_given_to' => 'Employee',
            'status' => 'In Progress',
            'progress' => 50,
        ]);

        $response = $this->put(route('tasks.update', $task), $updatedPayload);
        $response->assertRedirect(route('tasks.index'));

        // Check notifications (one from creation, one from update)
        $updateNotification = Notification::where('type', 'updated')
            ->where('task_id', $task->item_no)
            ->firstOrFail();

        $this->assertSame('Employee', $updateNotification->actor);
        $this->assertSame('all', $updateNotification->target_actor);
        $this->assertTrue($updateNotification->read_by_employee);
        $this->assertFalse($updateNotification->read_by_director);
        $this->assertFalse($updateNotification->read_by_manager);
        $this->assertStringContainsString('Employee updated task \'Build frontend\'', $updateNotification->message);
        $this->assertStringContainsString('status changed to \'In Progress\'', $updateNotification->message);
        $this->assertStringContainsString('progress updated to 50%', $updateNotification->message);
    }

    public function test_mark_all_read_clears_notifications_for_current_actor(): void
    {
        $task = Task::create($this->validTaskPayload([
            'project_name' => 'Test Task',
        ]));

        // Let's create two notifications targeted to Employee and all
        Notification::create([
            'task_id' => $task->item_no,
            'actor' => 'Infra Director',
            'type' => 'created',
            'message' => 'Task for employee',
            'target_actor' => 'Employee',
            'read_by_director' => true,
            'read_by_manager' => false,
            'read_by_employee' => false,
        ]);

        Notification::create([
            'task_id' => $task->item_no,
            'actor' => 'Project Manager',
            'type' => 'updated',
            'message' => 'Update for all',
            'target_actor' => 'all',
            'read_by_director' => false,
            'read_by_manager' => true,
            'read_by_employee' => false,
        ]);

        // As Employee, mark all as read
        session(['active_actor' => 'Employee']);

        $response = $this->post(route('notifications.read-all'));
        $response->assertRedirect();

        // Check read status
        $notifications = Notification::where('task_id', $task->item_no)->get();
        foreach ($notifications as $notif) {
            $this->assertTrue($notif->read_by_employee);
        }
        
        // Assert manager's or director's flags are unaffected unless they were the creators
        $this->assertFalse($notifications[0]->read_by_manager);
        $this->assertFalse($notifications[1]->read_by_director);
    }
}
