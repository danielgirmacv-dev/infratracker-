<?php

namespace Tests\Feature;

use App\Models\Manager;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session(['active_actor' => 'Infra Director']);

        Manager::create(['name' => 'PCOORD']);
        User::create([
            'name' => 'PCOORD',
            'email' => 'pcoord@infratracker.local',
            'password' => 'manager123',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(
            ['name' => 'FEVEN'],
            [
                'email' => 'feven@infratracker.local',
                'password' => 'employee123',
                'must_change_password' => false,
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
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
            'task_given_to' => 'PCOORD',
            'remark' => 'Notes here',
        ], $overrides);
    }

    public function test_store_with_valid_data_returns_302(): void
    {
        $response = $this->post(route('tasks.store'), $this->validTaskPayload());

        $response->assertStatus(302);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'project_name' => 'Project Alpha',
            'status' => 'Pending',
        ]);
    }

    public function test_store_with_invalid_data_returns_validation_errors(): void
    {
        $payload = $this->validTaskPayload([
            'project_name' => '',
            'start_date' => '2026-06-01',
            'end_date' => '2026-01-01',
        ]);

        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), $payload);

        $response->assertSessionHasErrors(['project_name', 'end_date']);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_update_persists_changes(): void
    {
        $task = Task::create($this->validTaskPayload());

        $updated = $this->validTaskPayload([
            'project_name' => 'Renamed',
            'progress' => 100,
        ]);

        $response = $this->put(route('tasks.update', $task), $updated);

        $response->assertRedirect(route('tasks.index'));

        $task->refresh();
        $this->assertSame('Renamed', $task->project_name);
        $this->assertSame(100, $task->progress);
    }

    public function test_store_with_all_required_fields_blank_shows_validation_errors_on_page(): void
    {
        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'date',
            'project_name',
            'task_description',
            'priority',
            'task_given_to',
        ]);

        $page = $this->get(route('tasks.create'));
        foreach ([
            'The date field is required.',
            'The project name field is required.',
            'The task description field is required.',
            'The priority field is required.',
            'The task given to field is required.',
        ] as $message) {
            $page->assertSee($message, false);
        }
    }

    public function test_index_filtered_by_status_returns_only_matching_tasks(): void
    {
        Task::create($this->validTaskPayload([
            'status' => 'Pending',
            'project_name' => 'UNIQUE_PENDING_PROJECT',
        ]));
        Task::create($this->validTaskPayload([
            'status' => 'Completed',
            'project_name' => 'UNIQUE_COMPLETED_PROJECT',
        ]));

        $response = $this->get(route('tasks.index', ['status' => 'Pending']));

        $response->assertOk();
        $response->assertSee('UNIQUE_PENDING_PROJECT');
        $response->assertDontSee('UNIQUE_COMPLETED_PROJECT');
    }

    public function test_delete_request_removes_task_from_database(): void
    {
        $task = Task::create($this->validTaskPayload());
        $key = $task->item_no;

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['item_no' => $key]);
    }

    public function test_director_can_edit_task_assigned_to_employee(): void
    {
        $task = Task::create($this->validTaskPayload([
            'task_given_to' => 'FEVEN',
            'task_given_by' => 'Infra Director',
        ]));

        $response = $this->get(route('tasks.edit', $task));
        $response->assertOk();
        $response->assertSee('Edit Task');

        $responseUpdate = $this->put(route('tasks.update', $task), $this->validTaskPayload([
            'status' => 'In Progress',
            'progress' => 25,
        ]));
        $responseUpdate->assertRedirect(route('tasks.index'));
        $responseUpdate->assertSessionHas('success', 'Task updated successfully.');

        $responseDelete = $this->delete(route('tasks.destroy', $task));
        $responseDelete->assertRedirect(route('tasks.index'));
        $responseDelete->assertSessionHas('success', 'Task deleted.');
    }

    public function test_employee_cannot_create_tasks(): void
    {
        session(['active_actor' => 'FEVEN', 'active_role' => 'Employee']);

        $responseCreate = $this->get(route('tasks.create'));
        $responseCreate->assertRedirect(route('tasks.index'));
        $responseCreate->assertSessionHas('error', 'Employees are not authorized to create tasks.');

        $responseStore = $this->post(route('tasks.store'), $this->validTaskPayload());
        $responseStore->assertRedirect(route('tasks.index'));
        $responseStore->assertSessionHas('error', 'Employees are not authorized to create tasks.');
    }

    public function test_employee_cannot_edit_other_peoples_tasks(): void
    {
        $task = Task::create($this->validTaskPayload([
            'task_given_to' => 'PCOORD'
        ]));

        session(['active_actor' => 'FEVEN', 'active_role' => 'Employee']);

        $responseEdit = $this->get(route('tasks.edit', $task));
        $responseEdit->assertRedirect(route('tasks.index'));
        $responseEdit->assertSessionHas('error', 'You are only authorized to edit tasks assigned to you.');

        $responseUpdate = $this->put(route('tasks.update', $task), $this->validTaskPayload());
        $responseUpdate->assertRedirect(route('tasks.index'));
        $responseUpdate->assertSessionHas('error', 'You are only authorized to edit tasks assigned to you.');
    }

    public function test_supplier_is_created_on_the_fly_when_storing_and_updating_task(): void
    {
        $this->assertDatabaseMissing('suppliers', ['name' => 'Brand New Supplier']);

        // Create task with new supplier
        $payload = $this->validTaskPayload([
            'supplier_name' => 'Brand New Supplier'
        ]);
        $response = $this->post(route('tasks.store'), $payload);
        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('suppliers', ['name' => 'Brand New Supplier']);

        // Update task with another new supplier
        $task = Task::where('supplier_name', 'Brand New Supplier')->firstOrFail();
        
        $this->assertDatabaseMissing('suppliers', ['name' => 'Second New Supplier']);
        
        $payload['supplier_name'] = 'Second New Supplier';
        $responseUpdate = $this->put(route('tasks.update', $task), $payload);
        $responseUpdate->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('suppliers', ['name' => 'Second New Supplier']);
    }
}
