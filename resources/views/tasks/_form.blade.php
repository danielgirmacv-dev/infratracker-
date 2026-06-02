@php
    $t = $task ?? null;
    $progressInitial = (int) old('progress', $t?->progress ?? 0);
    $inp = 'form-input @error(\'%s\') error @enderror';
@endphp

{{-- ── Section: Basic Info ───────────────────────────────────── --}}
<div class="form-section">
    <p class="form-section-title">Basic Info</p>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

        @if($t)
        <div class="sm:col-span-2">
            <label for="item_no" class="form-label">Item No.</label>
            <input id="item_no" type="text" readonly disabled
                class="form-input cursor-not-allowed opacity-50"
                value="{{ $t->item_no }}">
        </div>
        @else
        <div class="sm:col-span-2 rounded-lg border border-dashed border-indigo-500/20 bg-indigo-50 px-4 py-3 text-sm text-slate-500 dark:bg-indigo-500/5 dark:text-slate-400">
            <span class="font-medium text-slate-700 dark:text-slate-300">Item No.</span>
            — Assigned automatically when you save.
        </div>
        @endif

        <div>
            <label for="date" class="form-label">Date</label>
            <input id="date" type="date" name="date" value="{{ old('date', $t?->date?->format('Y-m-d')) }}"
                class="form-input @error('date') error @enderror" {{ $activeActor === 'Employee' ? 'readonly' : '' }}>
            @error('date')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="project_name" class="form-label">Project Name <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="project_name" name="project_name"
                class="form-input @error('project_name') error @enderror" {{ $activeActor === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select project…</option>
                @foreach(\App\Models\Project::orderBy('name')->pluck('name') as $pname)
                    <option value="{{ $pname }}" @selected(old('project_name', $t?->project_name) === $pname)>{{ $pname }}</option>
                @endforeach
            </select>
            @if($activeActor === 'Employee')
                <input type="hidden" name="project_name" value="{{ old('project_name', $t?->project_name) }}">
            @endif
            @error('project_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="task_description" class="form-label">Task Description</label>
            <textarea id="task_description" name="task_description" rows="3"
                placeholder="Describe the task in detail…"
                class="form-input @error('task_description') error @enderror" {{ $activeActor === 'Employee' ? 'readonly' : '' }}>{{ old('task_description', $t?->task_description) }}</textarea>
            @error('task_description')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <select id="supplier_name" name="supplier_name"
                class="form-input @error('supplier_name') error @enderror" {{ $activeActor === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select supplier…</option>
                @foreach(\App\Models\Supplier::orderBy('name')->pluck('name') as $sname)
                    <option value="{{ $sname }}" @selected(old('supplier_name', $t?->supplier_name) === $sname)>{{ $sname }}</option>
                @endforeach
            </select>
            @if($activeActor === 'Employee')
                <input type="hidden" name="supplier_name" value="{{ old('supplier_name', $t?->supplier_name) }}">
            @endif
            @error('supplier_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="amount" class="form-label">Amount (ETB)</label>
            <div class="currency-input flex overflow-hidden rounded-lg border border-indigo-500/15">
                <span class="inline-flex items-center border-r border-indigo-500/15 bg-slate-100 px-3 text-xs font-semibold text-slate-500 dark:bg-white/[0.03] dark:text-slate-400">ETB</span>
                <input id="amount" type="number" name="amount" step="0.01" min="0"
                    value="{{ old('amount', $t?->amount) }}" placeholder="0.00"
                    {{ $activeActor === 'Employee' ? 'readonly' : '' }}
                    class="block min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-0 @error('amount') border-red-500 @enderror">
            </div>
            @error('amount')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

    </div>
</div>

{{-- ── Section: Schedule ─────────────────────────────────────── --}}
@if($t)
<div class="form-section">
    <p class="form-section-title">Schedule</p>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

        <div>
            <label for="start_date" class="form-label">Start Date</label>
            <input id="start_date" type="date" name="start_date"
                value="{{ old('start_date', $t?->start_date?->format('Y-m-d')) }}"
                class="form-input @error('start_date') error @enderror">
            @error('start_date')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="end_date" class="form-label">End Date</label>
            <input id="end_date" type="date" name="end_date"
                value="{{ old('end_date', $t?->end_date?->format('Y-m-d')) }}"
                class="form-input @error('end_date') error @enderror">
            @error('end_date')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

    </div>
</div>
@endif

{{-- ── Section: Status & Progress (Conditional) ────────────────── --}}
@if($t)
<div class="form-section">
    <p class="form-section-title">Status &amp; Progress</p>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

        <div>
            <label for="status" class="form-label">Status <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="status" name="status" class="form-input @error('status') error @enderror">
                <option value="">Select status…</option>
                @foreach(['Pending','In Progress','Completed','On Hold'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $t?->status) === $s)>{{ $s }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="priority" class="form-label">Priority <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="priority" name="priority" class="form-input @error('priority') error @enderror" {{ $activeActor === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select priority…</option>
                @foreach(['Low','Medium','High','Critical'] as $p)
                    <option value="{{ $p }}" @selected(old('priority', $t?->priority) === $p)>{{ $p }}</option>
                @endforeach
            </select>
            @if($activeActor === 'Employee')
                <input type="hidden" name="priority" value="{{ old('priority', $t?->priority) }}">
            @endif
            @error('priority')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2" x-data="{ val: {{ $progressInitial }} }">
            <label for="progress" class="form-label">
                Progress
                <span class="ml-1 font-bold text-indigo-600 dark:text-indigo-400" x-text="val + '%'"></span>
            </label>
            <div class="mt-2 flex items-center gap-4">
                <input
                    id="progress" type="range" name="progress" min="0" max="100" step="1"
                    x-model="val"
                    class="h-2 w-full cursor-pointer appearance-none rounded-lg accent-indigo-500"
                    style="background: linear-gradient(to right, #6366f1 0%, #6366f1 calc(var(--v, {{ $progressInitial }})*1%), rgba(255,255,255,0.07) calc(var(--v, {{ $progressInitial }})*1%), rgba(255,255,255,0.07) 100%)"
                    @input="$el.style.setProperty('--v', val)"
                >
                <div class="w-28 shrink-0">
                    <div class="progress-track">
                        <div class="progress-fill" :style="'width:' + val + '%'"></div>
                    </div>
                </div>
            </div>
            @error('progress')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

    </div>
</div>
@else
{{-- If creating a task, status defaults to Pending and progress to 0%, but we still need to set Priority! --}}
<div class="form-section">
    <p class="form-section-title">Priority</p>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
        <div>
            <label for="priority" class="form-label">Priority <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="priority" name="priority" class="form-input @error('priority') error @enderror">
                <option value="">Select priority…</option>
                @foreach(['Low','Medium','High','Critical'] as $p)
                    <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                @endforeach
            </select>
            @error('priority')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
@endif

{{-- ── Section: Assignment ───────────────────────────────────── --}}
<div class="form-section">
    <p class="form-section-title">Assignment</p>
    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

        @if($t)
        <div>
            <label for="next_action" class="form-label">Next Action</label>
            <input id="next_action" type="text" name="next_action" maxlength="255"
                value="{{ old('next_action', $t?->next_action) }}"
                placeholder="What needs to happen next?"
                class="form-input @error('next_action') error @enderror">
            @error('next_action')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="responsible_department" class="form-label">Responsible Department</label>
            <input id="responsible_department" type="text" name="responsible_department" maxlength="255"
                value="{{ old('responsible_department', $t?->responsible_department) }}"
                placeholder="e.g. Engineering, Procurement"
                class="form-input @error('responsible_department') error @enderror">
            @error('responsible_department')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        @endif

        <div>
            <label for="task_given_by" class="form-label">Task Given By</label>
            <input id="task_given_by" type="text" readonly disabled
                class="form-input cursor-not-allowed opacity-70 bg-slate-100 dark:bg-slate-800"
                value="{{ $t ? $t->task_given_by : $activeActor }}">
        </div>

        <div>
            <label for="task_given_to" class="form-label">Task Given To <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="task_given_to" name="task_given_to" class="form-input @error('task_given_to') error @enderror" {{ $activeActor === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select assignee…</option>
                @if($activeActor === 'Infra Director')
                    <option value="Project Manager" @selected(old('task_given_to', $t?->task_given_to) === 'Project Manager')>Project Coordinator</option>
                    <option value="Employee" @selected(old('task_given_to', $t?->task_given_to) === 'Employee')>Feven</option>
                @elseif($activeActor === 'Project Manager')
                    <option value="Employee" @selected(old('task_given_to', $t?->task_given_to) === 'Employee')>Feven</option>
                @else
                    <option value="Employee" @selected(old('task_given_to', $t?->task_given_to) === 'Employee')>Feven</option>
                @endif
            </select>
            @if($activeActor === 'Employee')
                <input type="hidden" name="task_given_to" value="Employee">
            @endif
            @error('task_given_to')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="remark" class="form-label">Remark</label>
            <textarea id="remark" name="remark" rows="3"
                placeholder="Any additional notes…"
                class="form-input @error('remark') error @enderror">{{ old('remark', $t?->remark) }}</textarea>
            @error('remark')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

    </div>
</div>
