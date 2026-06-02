<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'project_name' => ['required', 'string', 'max:255'],
            'task_description' => ['required', 'string'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => [$this->isMethod('post') ? 'nullable' : 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'next_action' => ['nullable', 'string', 'max:255'],
            'responsible_department' => [$this->isMethod('post') ? 'nullable' : 'required', 'string', 'max:255'],
            'task_given_by' => ['nullable', 'string', Rule::in(['Infra Director', 'Project Manager', 'Employee'])],
            'task_given_to' => ['required', 'string', Rule::in(['Infra Director', 'Project Manager', 'Employee'])],
            'remark' => ['nullable', 'string'],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['status'] = ['required', Rule::in(['Pending', 'In Progress', 'Completed', 'On Hold'])];
            $rules['progress'] = ['required', 'integer', 'between:0,100'];
        }

        return $rules;
    }
}
