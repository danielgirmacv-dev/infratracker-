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
                class="form-input @error('date') error @enderror" {{ ($activeRole ?? '') === 'Employee' ? 'readonly' : '' }}>
            @error('date')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="project_name" class="form-label">Project Name <span class="text-red-600 dark:text-red-400">*</span></label>
            <select id="project_name" name="project_name"
                class="form-input @error('project_name') error @enderror" {{ ($activeRole ?? '') === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select project…</option>
                @foreach(\App\Models\Project::orderBy('name')->pluck('name') as $pname)
                    <option value="{{ $pname }}" @selected(old('project_name', $t?->project_name) === $pname)>{{ $pname }}</option>
                @endforeach
            </select>
            @if(($activeRole ?? '') === 'Employee')
                <input type="hidden" name="project_name" value="{{ old('project_name', $t?->project_name) }}">
            @endif
            @error('project_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="task_description" class="form-label">Task Description</label>
            <textarea id="task_description" name="task_description" rows="3"
                placeholder="Describe the task in detail…"
                class="form-input @error('task_description') error @enderror" {{ ($activeRole ?? '') === 'Employee' ? 'readonly' : '' }}>{{ old('task_description', $t?->task_description) }}</textarea>
            @error('task_description')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="supplier_name" class="form-label">Supplier Name</label>
            <input id="supplier_name" name="supplier_name" list="suppliers_list"
                type="text"
                placeholder="Enter or select supplier..."
                value="{{ old('supplier_name', $t?->supplier_name) }}"
                class="form-input @error('supplier_name') error @enderror" {{ ($activeRole ?? '') === 'Employee' ? 'disabled' : '' }}>
            <datalist id="suppliers_list">
                @foreach(\App\Models\Supplier::orderBy('name')->pluck('name') as $sname)
                    <option value="{{ $sname }}">
                @endforeach
            </datalist>
            @if(($activeRole ?? '') === 'Employee')
                <input type="hidden" name="supplier_name" value="{{ old('supplier_name', $t?->supplier_name) }}">
            @endif
            @error('supplier_name')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="amount" class="form-label">Amount (ETB)</label>
            <div class="currency-input flex overflow-hidden rounded-lg border border-indigo-500/15">
                <span class="inline-flex items-center border-r border-indigo-500/15 bg-slate-100 px-3 text-xs font-semibold text-slate-500 dark:bg-white/[0.03] dark:text-slate-400">ETB</span>
                <input
                    id="amount"
                    type="text"
                    name="amount"
                    inputmode="decimal"
                    value="{{ \App\Support\MoneyFormat::format(old('amount', $t?->amount)) }}"
                    placeholder="e.g. 12,527,000"
                    {{ ($activeRole ?? '') === 'Employee' ? 'readonly' : '' }}
                    class="comma-number-input block min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-0 @error('amount') border-red-500 @enderror"
                    autocomplete="off"
                >
            </div>
            <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">Cost in ETB, e.g. 12,527,000</p>
            @error('amount')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="quantity" class="form-label">Quantities</label>
            <div class="currency-input flex overflow-hidden rounded-lg border border-indigo-500/15">
                <select
                    id="quantity_unit"
                    name="quantity_unit"
                    {{ ($activeRole ?? '') === 'Employee' ? 'disabled' : '' }}
                    class="max-w-[5.5rem] shrink-0 cursor-pointer border-r border-indigo-500/15 bg-slate-100 px-2 py-2.5 text-xs font-semibold text-slate-600 focus:outline-none focus:ring-0 dark:bg-white/[0.03] dark:text-slate-300 @error('quantity_unit') border-red-500 @enderror"
                >
                    <option value="">Unit</option>
                    @foreach(\App\Support\QuantityUnit::options() as $unit)
                        <option value="{{ $unit }}" @selected(old('quantity_unit', $t?->quantity_unit) === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
                @if(($activeRole ?? '') === 'Employee')
                    <input type="hidden" name="quantity_unit" value="{{ old('quantity_unit', $t?->quantity_unit) }}">
                @endif
                <input
                    id="quantity"
                    type="text"
                    name="quantity"
                    inputmode="decimal"
                    value="{{ \App\Support\MoneyFormat::format(old('quantity', $t?->quantity)) }}"
                    placeholder="e.g. 50,000"
                    {{ ($activeRole ?? '') === 'Employee' ? 'readonly' : '' }}
                    class="comma-number-input block min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-0 @error('quantity') border-red-500 @enderror"
                    autocomplete="off"
                >
            </div>
            <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">Select a unit, then enter the quantity (e.g. 50,000 Kg)</p>
            @error('quantity')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            @error('quantity_unit')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
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
            <select id="priority" name="priority" class="form-input @error('priority') error @enderror" {{ ($activeRole ?? '') === 'Employee' ? 'disabled' : '' }}>
                <option value="">Select priority…</option>
                @foreach(['Low','Medium','High','Critical'] as $p)
                    <option value="{{ $p }}" @selected(old('priority', $t?->priority) === $p)>{{ $p }}</option>
                @endforeach
            </select>
            @if(($activeRole ?? '') === 'Employee')
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
            <label for="task_given_to_search" class="form-label">Task Given To <span class="text-red-600 dark:text-red-400">*</span></label>
            @php
                $taskGiver = $t ? $t->task_given_by : ($activeActor ?? '');
                $assigneeOptions = [];

                // Coordinators (Director only)
                if (($activeRole ?? '') === 'Infra Director') {
                    foreach ([['name' => 'Biruk', 'role' => 'Coordinator'], ['name' => 'Feven', 'role' => 'Coordinator Assistance']] as $coord) {
                        if ($coord['name'] !== $taskGiver) {
                            $assigneeOptions[] = ['value' => $coord['name'], 'label' => $coord['name'] . ' (' . $coord['role'] . ')', 'group' => 'Coordinators'];
                        }
                    }
                }

                // Project Managers (Director & PM)
                if (in_array($activeRole ?? '', ['Infra Director', 'Project Manager'])) {
                    foreach ($managers ?? [] as $manager) {
                        if ($manager->name !== $taskGiver) {
                            $assigneeOptions[] = ['value' => $manager->name, 'label' => $manager->name, 'group' => 'Project Managers'];
                        }
                    }
                }

                // Employees (everyone)
                foreach ($employees ?? [] as $employee) {
                    if ($employee->name !== $taskGiver) {
                        $assigneeOptions[] = ['value' => $employee->name, 'label' => $employee->name, 'group' => 'Employees'];
                    }
                }

                $currentAssignee = old('task_given_to', $t?->task_given_to ?? '');
                $currentLabel = '';
                foreach ($assigneeOptions as $opt) {
                    if ($opt['value'] === $currentAssignee) { $currentLabel = $opt['label']; break; }
                }
            @endphp

            @if(($activeRole ?? '') === 'Employee')
                {{-- Employees can't change assignee --}}
                <input type="text" readonly disabled class="form-input cursor-not-allowed opacity-70 bg-slate-100 dark:bg-slate-800" value="{{ $activeActor }}">
                <input type="hidden" name="task_given_to" value="{{ $activeActor }}">
            @else
                <div x-data="{
                    open: false,
                    search: '{{ addslashes($currentLabel) }}',
                    selected: '{{ addslashes($currentAssignee) }}',
                    highlightIdx: -1,
                    options: {{ Js::from($assigneeOptions) }},
                    get groups() {
                        const q = this.search.toLowerCase().trim();
                        const filtered = this.options.filter(o =>
                            !q || o.value === this.selected || o.label.toLowerCase().includes(q) || o.value.toLowerCase().includes(q)
                        );
                        const g = {};
                        filtered.forEach(o => { (g[o.group] = g[o.group] || []).push(o); });
                        return g;
                    },
                    get flatFiltered() {
                        const out = [];
                        for (const [, items] of Object.entries(this.groups)) { out.push(...items); }
                        return out;
                    },
                    pick(opt) {
                        this.selected = opt.value;
                        this.search = opt.label;
                        this.open = false;
                        this.highlightIdx = -1;
                    },
                    clear() {
                        this.selected = '';
                        this.search = '';
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    },
                    onArrowDown() {
                        if (!this.open) { this.open = true; return; }
                        this.highlightIdx = Math.min(this.highlightIdx + 1, this.flatFiltered.length - 1);
                        this.scrollToHighlighted();
                    },
                    onArrowUp() {
                        this.highlightIdx = Math.max(this.highlightIdx - 1, 0);
                        this.scrollToHighlighted();
                    },
                    onEnter() {
                        if (this.highlightIdx >= 0 && this.highlightIdx < this.flatFiltered.length) {
                            this.pick(this.flatFiltered[this.highlightIdx]);
                        }
                    },
                    scrollToHighlighted() {
                        this.$nextTick(() => {
                            const el = this.$refs.listbox?.querySelector('[data-highlighted=true]');
                            if (el) el.scrollIntoView({ block: 'nearest' });
                        });
                    }
                }"
                @click.outside="open = false"
                class="relative">
                    {{-- Hidden real input --}}
                    <input type="hidden" name="task_given_to" :value="selected">

                    {{-- Search text input --}}
                    <div class="relative">
                        <input
                            x-ref="searchInput"
                            id="task_given_to_search"
                            type="text"
                            autocomplete="off"
                            placeholder="Search or select assignee…"
                            class="form-input pr-16 @error('task_given_to') error @enderror"
                            x-model="search"
                            @focus="open = true; highlightIdx = -1; if (selected) search = ''"
                            @input="open = true; highlightIdx = 0; selected = ''"
                            @keydown.arrow-down.prevent="onArrowDown()"
                            @keydown.arrow-up.prevent="onArrowUp()"
                            @keydown.enter.prevent="onEnter()"
                            @keydown.escape="open = false; if (selected) { search = options.find(o => o.value === selected)?.label || selected; }"
                            @blur="if (!selected && search) { const match = flatFiltered[0]; if (match) pick(match); else search = ''; }"
                        >
                        {{-- Clear button --}}
                        <button
                            type="button"
                            x-show="selected"
                            @click.stop="clear()"
                            class="absolute inset-y-0 right-8 flex items-center px-1 text-slate-400 hover:text-red-500 transition-colors"
                            title="Clear selection"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        {{-- Dropdown toggle --}}
                        <button
                            type="button"
                            @click.stop="open = !open; if (open) $refs.searchInput.focus()"
                            class="absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                        >
                            <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    {{-- Dropdown list --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        x-ref="listbox"
                        class="absolute z-50 mt-1.5 w-full max-h-56 overflow-auto rounded-xl border border-indigo-500/15 bg-white shadow-xl shadow-slate-900/10 ring-1 ring-black/5 dark:bg-slate-800 dark:border-slate-600/30 dark:shadow-black/30 dark:ring-white/5"
                        style="display: none;"
                    >
                        <template x-if="flatFiltered.length === 0">
                            <div class="px-4 py-3 text-sm text-slate-400 dark:text-slate-500 text-center italic">
                                No matching actors found
                            </div>
                        </template>

                        <template x-for="(groupName, gi) in Object.keys(groups)" :key="groupName">
                            <div>
                                {{-- Group header --}}
                                <div class="sticky top-0 z-10 px-3 py-1.5 text-[10px] font-bold tracking-wider uppercase text-indigo-500/70 dark:text-indigo-400/60 bg-slate-50/95 dark:bg-slate-800/95 backdrop-blur-sm border-b border-slate-100 dark:border-slate-700/50"
                                     :class="gi > 0 && 'border-t'"
                                     x-text="groupName"></div>

                                {{-- Group items --}}
                                <template x-for="(opt, oi) in groups[groupName]" :key="opt.value">
                                    <div
                                        @click="pick(opt)"
                                        @mouseenter="highlightIdx = flatFiltered.indexOf(opt)"
                                        :data-highlighted="highlightIdx === flatFiltered.indexOf(opt)"
                                        class="flex items-center gap-2.5 px-3 py-2 text-sm cursor-pointer transition-colors"
                                        :class="{
                                            'bg-indigo-500 text-white': highlightIdx === flatFiltered.indexOf(opt),
                                            'text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-500/10': highlightIdx !== flatFiltered.indexOf(opt),
                                        }"
                                    >
                                        {{-- Avatar circle --}}
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold"
                                              :class="{
                                                  'bg-white/20 text-white': highlightIdx === flatFiltered.indexOf(opt),
                                                  'bg-indigo-500/10 text-indigo-600 dark:bg-indigo-400/15 dark:text-indigo-400': highlightIdx !== flatFiltered.indexOf(opt) && opt.group === 'Coordinators',
                                                  'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/15 dark:text-emerald-400': highlightIdx !== flatFiltered.indexOf(opt) && opt.group === 'Project Managers',
                                                  'bg-amber-500/10 text-amber-600 dark:bg-amber-400/15 dark:text-amber-400': highlightIdx !== flatFiltered.indexOf(opt) && opt.group === 'Employees',
                                              }"
                                              x-text="opt.value.substring(0, 2).toUpperCase()"></span>

                                        <span class="truncate" x-text="opt.label"></span>

                                        {{-- Checkmark when selected --}}
                                        <svg x-show="selected === opt.value" class="ml-auto h-4 w-4 shrink-0" :class="highlightIdx === flatFiltered.indexOf(opt) ? 'text-white' : 'text-indigo-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
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

<script>
    (function () {
        function formatCommaNumber(raw) {
            var value = String(raw).replace(/[^\d.]/g, '');
            var dotIndex = value.indexOf('.');
            var intPart = dotIndex === -1 ? value : value.slice(0, dotIndex);
            var decPart = dotIndex === -1 ? '' : value.slice(dotIndex + 1).replace(/\./g, '').slice(0, 2);

            intPart = intPart.replace(/^0+(?=\d)/, '');
            if (intPart === '' && (decPart !== '' || value.includes('.'))) {
                intPart = '0';
            }

            var formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (dotIndex !== -1) {
                formatted += '.' + decPart;
            }

            return formatted;
        }

        document.querySelectorAll('.comma-number-input').forEach(function (input) {
            if (input.readOnly) return;

            input.addEventListener('input', function () {
                input.value = formatCommaNumber(input.value);
            });

            input.addEventListener('blur', function () {
                if (input.value.endsWith('.')) {
                    input.value = input.value.slice(0, -1);
                }
            });
        });
    })();
</script>
