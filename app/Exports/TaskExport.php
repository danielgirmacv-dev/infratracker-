<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaskExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * @param  Builder<Task>  $query
     */
    public function __construct(
        private Builder $query
    ) {}

    /**
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        return $this->query->clone()->orderBy('item_no');
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'item_no',
            'date',
            'project_name',
            'task_description',
            'supplier_name',
            'amount',
            'start_date',
            'end_date',
            'status',
            'priority',
            'progress',
            'next_action',
            'responsible_department',
            'task_given_to',
            'remark',
        ];
    }

    /**
     * @param  Task  $task
     * @return array<int, mixed>
     */
    public function map($task): array
    {
        return [
            $task->item_no,
            $task->date?->format('Y-m-d'),
            $task->project_name,
            $task->task_description,
            $task->supplier_name,
            $task->amount,
            $task->start_date?->format('Y-m-d'),
            $task->end_date?->format('Y-m-d'),
            $task->status,
            $task->priority,
            $task->progress,
            $task->next_action,
            $task->responsible_department,
            $task->task_given_to,
            $task->remark,
        ];
    }
}
