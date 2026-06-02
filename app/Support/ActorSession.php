<?php

namespace App\Support;

use App\Models\Employee;

class ActorSession
{
    public static function name(): string
    {
        return (string) session('active_actor', 'Infra Director');
    }

    public static function role(): string
    {
        return (string) session('active_role', 'Infra Director');
    }

    public static function isDirector(): bool
    {
        return self::role() === 'Infra Director';
    }

    public static function isManager(): bool
    {
        return self::role() === 'Project Manager';
    }

    public static function isEmployee(): bool
    {
        return self::role() === 'Employee';
    }

    public static function canManageTasks(): bool
    {
        return self::isDirector() || self::isManager();
    }

    public static function isTaskAssignee(string $assignee): bool
    {
        if ($assignee === 'Employee') {
            return true;
        }

        return Employee::isEmployeeName($assignee);
    }

    public static function loginRoleForActor(string $actor): string
    {
        if (in_array($actor, ['Infra Director', 'Project Manager'], true)) {
            return $actor;
        }

        if ($actor === 'Employee') {
            return 'Employee';
        }

        return Employee::isEmployeeName($actor) ? 'Employee' : $actor;
    }
}
