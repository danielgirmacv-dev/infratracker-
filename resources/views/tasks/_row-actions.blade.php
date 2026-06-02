@php
    $canManage = in_array($activeActor, ['Infra Director', 'Project Manager'], true);
    $isAssignedToEmployee = $task->task_given_to === 'Employee';
    $showLockBadge = $isAssignedToEmployee && $activeActor === ($task->task_given_by ?? 'Infra Director');
    $isEditable = $canManage || ($activeActor === 'Employee' && $isAssignedToEmployee);
    $iconClass = $iconClass ?? 'h-4 w-4';
    $btnPadding = $btnPadding ?? 'p-1.5';
@endphp
<div class="inline-flex items-center gap-1 pr-1">
    @if($showLockBadge)
        <span class="inline-flex rounded-lg {{ $btnPadding }} text-amber-600 dark:text-amber-400 bg-amber-100/80 dark:bg-amber-500/10" title="Assigned to employee — you can still edit">
            <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
        </span>
    @endif
    @if($isEditable)
        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex rounded-lg {{ $btnPadding }} text-indigo-600 dark:text-indigo-400 transition hover:bg-indigo-100 dark:bg-indigo-500/10" title="Edit">
            <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
        </a>
        @if($canManage)
            <button
                type="button"
                class="inline-flex rounded-lg {{ $btnPadding }} text-red-600 dark:text-red-400 transition hover:bg-red-100 dark:bg-red-500/10"
                title="Delete"
                @click="deleteModalOpen=true; deleteUrl=@js(route('tasks.destroy',$task)); deleteLabel=@js($task->project_name)"
            >
                <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
            </button>
        @endif
    @elseif(!$showLockBadge)
        <span class="inline-flex rounded-lg {{ $btnPadding }} text-slate-400 dark:text-slate-600 bg-slate-100 dark:bg-white/5 cursor-not-allowed" title="You cannot edit this task">
            <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
        </span>
    @endif
</div>
