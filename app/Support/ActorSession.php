<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\Manager;

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
        return in_array(self::role(), ['Infra Director', 'Coordinator', 'Coordinator Assistance'], true);
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

    public static function canManageEmployees(): bool
    {
        return self::isDirector();
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
        if ($actor === 'Infra Director') {
            return 'Infra Director';
        }

        if ($actor === 'Coordinator') {
            return 'Coordinator';
        }

        if ($actor === 'Coordinator Assistance') {
            return 'Coordinator Assistance';
        }

        if ($actor === 'Project Manager' || Manager::isManagerName($actor)) {
            return 'Project Manager';
        }

        if ($actor === 'Employee') {
            return 'Employee';
        }

        return Employee::isEmployeeName($actor) ? 'Employee' : $actor;
    }
}
