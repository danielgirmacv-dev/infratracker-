<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\Manager;
use App\Support\MoneyFormat;
use App\Support\QuantityUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => MoneyFormat::parse($this->input('amount')),
            ]);
        }

        if ($this->has('quantity')) {
            $this->merge([
                'quantity' => MoneyFormat::parse($this->input('quantity')),
            ]);
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeNames = Schema::hasTable('employees') ? Employee::assigneeOptions() : ['FEVEN'];
        $managerNames = Schema::hasTable('managers') ? Manager::assigneeOptions() : [];

        $assignees = array_values(array_filter(
            array_merge(['Infra Director'], $managerNames, $employeeNames),
            fn (string $name) => $name !== $this->taskGiverName()
        ));

        $rules = [
            'date' => ['required', 'date'],
            'project_name' => ['required', 'string', 'max:255'],
            'task_description' => ['required', 'string'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'quantity_unit' => ['nullable', 'string', Rule::in(QuantityUnit::options())],
            'start_date' => [$this->isMethod('post') ? 'nullable' : 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'next_action' => ['nullable', 'string', 'max:255'],
            'responsible_department' => [$this->isMethod('post') ? 'nullable' : 'required', 'string', 'max:255'],
            'task_given_by' => ['nullable', 'string', Rule::in($assignees)],
            'task_given_to' => ['required', 'string', Rule::in($assignees)],
            'remark' => ['nullable', 'string'],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['status'] = ['required', Rule::in(['Pending', 'In Progress', 'Completed', 'On Hold'])];
            $rules['progress'] = ['required', 'integer', 'between:0,100'];
        }

        return $rules;
    }

    private function taskGiverName(): string
    {
        $task = $this->route('task');

        if ($task) {
            return (string) $task->task_given_by;
        }

        return (string) $this->session()->get('active_actor', 'Infra Director');
    }
}
