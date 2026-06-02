<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskExportHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_export_returns_xlsx_response(): void
    {
        session(['active_actor' => 'Infra Director']);

        $response = $this->get(route('tasks.export'));

        $response->assertStatus(200);
        $response->assertHeader(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }
}
