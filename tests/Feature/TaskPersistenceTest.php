<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_record_can_be_retrieved_with_expected_column_values(): void
    {
        $task = Task::create([
            'date' => '2026-05-01',
            'project_name' => 'Bridge Rehab',
            'task_description' => 'Replace bearings on span 2.',
            'supplier_name' => null,
            'amount' => '12345.67',
            'start_date' => '2026-04-15',
            'end_date' => '2026-06-30',
            'status' => 'In Progress',
            'priority' => 'High',
            'progress' => 45,
            'next_action' => null,
            'responsible_department' => 'Engineering',
            'task_given_to' => 'Employee',
            'remark' => null,
        ]);

        $fromDb = Task::query()->findOrFail($task->item_no);

        $this->assertTrue($fromDb->date->isSameDay('2026-05-01'));
        $this->assertSame('Bridge Rehab', $fromDb->project_name);
        $this->assertSame('Replace bearings on span 2.', $fromDb->task_description);
        $this->assertNull($fromDb->supplier_name);
        $this->assertSame('12345.67', $fromDb->amount);
        $this->assertTrue($fromDb->start_date->isSameDay('2026-04-15'));
        $this->assertTrue($fromDb->end_date->isSameDay('2026-06-30'));
        $this->assertSame('In Progress', $fromDb->status);
        $this->assertSame('High', $fromDb->priority);
        $this->assertSame(45, $fromDb->progress);
        $this->assertNull($fromDb->next_action);
        $this->assertSame('Engineering', $fromDb->responsible_department);
        $this->assertSame('Employee', $fromDb->task_given_to);
        $this->assertNull($fromDb->remark);
    }
}
