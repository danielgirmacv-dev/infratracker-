<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'task_id',
        'actor',
        'type',
        'message',
        'target_actor',
        'read_by_director',
        'read_by_manager',
        'read_by_employee',
    ];

    protected $casts = [
        'read_by_director' => 'boolean',
        'read_by_manager' => 'boolean',
        'read_by_employee' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'item_no');
    }
}
