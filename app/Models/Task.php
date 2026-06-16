<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = 'item_no';

    public function getRouteKeyName(): string
    {
        return 'item_no';
    }

    protected $fillable = [
        'item_no',
        'date',
        'project_name',
        'task_description',
        'supplier_name',
        'amount',
        'quantity',
        'quantity_unit',
        'start_date',
        'end_date',
        'status',
        'priority',
        'progress',
        'next_action',
        'responsible_department',
        'task_given_by',
        'task_given_to',
        'remark',
    ];

    protected $casts = [
        'date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'decimal:2',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'task_id', 'item_no');
    }
}
