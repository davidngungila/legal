@extends('layouts.app')

@section('title', $session->title . ' - Training Session')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <a href="{{ route('training.programs.show', $session->program_id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1"></i> Back to {{ $session->program?->name ?? 'Program' }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope mt-2">{{ $session->title }}</h1>
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="px-2 py-1 text-sm font-medium rounded-full
                    {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : ($session->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($session->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">{{ ucwords(str_replace('_', ' ', $session->status)) }}</span>
                @if($session->plan)
                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">{{ $session->plan->name }}</span>
                @endif
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openModal('enrollModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Enroll Employees
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $session->enrollments->count() }}</h3>
            <p class="text-gray-600 text-sm">Enrolled</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $session->enrollments->where('attendance_status', 'present')->count() }}</h3>
            <p class="text-gray-600 text-sm">Present</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $session->enrollments->where('status', 'completed')->count() }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($session->enrollments->avg('assessment_score') ?? 0, 1) }}%</h3>
            <p class="text-gray-600 text-sm">Avg Assessment Score</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Session Details</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Instructor</dt><dd class="text-sm font-medium">{{ $session->instructor ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Venue</dt><dd class="text-sm font-medium">{{ $session->venue ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Start</dt><dd class="text-sm font-medium">{{ $session->start_at ? $session->start_at->format('M d, Y H:i') : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">End</dt><dd class="text-sm font-medium">{{ $session->end_at ? $session->end_at->format('M d, Y H:i') : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Capacity</dt><dd class="text-sm font-medium">{{ $session->capacity ?: 'Unlimited' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Program</dt><dd class="text-sm font-medium">{{ $session->program?->name ?: '—' }}</dd></div>
            </dl>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Program Info</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Category</dt><dd class="text-sm font-medium">{{ $session->program?->category ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Cost</dt><dd class="text-sm font-medium">{{ $session->program ? number_format($session->program->cost, 0) . ' ' . $session->program->currency : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Duration</dt><dd class="text-sm font-medium">{{ $session->program?->duration_hours ? $session->program->duration_hours . ' hrs' : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Certification</dt><dd class="text-sm font-medium">{{ $session->program?->is_certification ? 'Yes' : 'No' }}</dd></div>
            </dl>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Enrollment Status</h3>
            <dl class="space-y-3">
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Enrolled</dt><dd class="text-sm font-medium">{{ $session->enrollments->where('status', 'enrolled')->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">In Progress</dt><dd class="text-sm font-medium">{{ $session->enrollments->where('status', 'in_progress')->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Completed</dt><dd class="text-sm font-medium">{{ $session->enrollments->where('status', 'completed')->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 text-sm">Dropped</dt><dd class="text-sm font-medium">{{ $session->enrollments->where('status', 'dropped')->count() }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Enrolled Employees</h3>
        </div>
        @if($session->enrollments->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passed</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($session->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <span class="text-indigo-700 font-semibold text-sm">{{ strtoupper(substr($enrollment->employee?->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($enrollment->employee?->last_name ?? '', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $enrollment->employee?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->employee?->employee_id }} · {{ $enrollment->employee?->department ?: '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <form action="{{ route('training.enrollments.score', $enrollment->id) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach(['enrolled', 'in_progress', 'completed', 'dropped'] as $s)
                                    <option value="{{ $s }}" {{ $enrollment->status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-4">
                            <form action="{{ route('training.enrollments.attendance', $enrollment->id) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')
                                <select name="attendance_status" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Not marked</option>
                                    @foreach(['present', 'absent', 'late'] as $a)
                                    <option value="{{ $a }}" {{ $enrollment->attendance_status === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-4">
                            <form action="{{ route('training.enrollments.score', $enrollment->id) }}" method="POST" class="flex items-center space-x-1">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="assessment_score" min="0" max="100" step="0.01" value="{{ $enrollment->assessment_score }}" class="w-20 text-xs rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800">Save</button>
                            </form>
                        </td>
                        <td class="px-4 py-4">
                            <form action="{{ route('training.enrollments.score', $enrollment->id) }}" method="POST" class="flex items-center space-x-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="passed" value="0">
                                <input type="checkbox" name="passed" value="1" onchange="this.form.submit()" {{ $enrollment->passed ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </form>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                @if($enrollment->status === 'completed')
                                <a href="{{ route('training.certificate', $enrollment->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    <i data-feather="download" class="w-4 h-4 mr-1"></i> Certificate
                                </a>
                                @endif
                                <form action="{{ route('training.enrollments.unenroll', $enrollment->id) }}" method="POST" onsubmit="return confirm('Remove this employee from the session?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-gray-500 py-12">No employees enrolled yet. Click "Enroll Employees" to add them.</div>
        @endif
    </div>
</div>

<x-advanced-modal id="enrollModal" title="Enroll Employees" icon="user-plus" color="indigo" size="lg">
    <form action="{{ route('training.sessions.bulkEnroll', $session->id) }}" method="POST" id="enrollForm">
        @csrf
        @if($employees->count())
        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-gray-600">Select one or more active employees not yet enrolled.</p>
            <button type="button" onclick="toggleAllEnroll()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Select all</button>
        </div>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            @foreach($employees as $employee)
            <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" class="enroll-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">{{ $employee->full_name }}</span>
                <span class="text-xs text-gray-500">{{ $employee->employee_id }} · {{ $employee->department ?: 'No department' }}</span>
            </label>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-600 py-4">All active employees are already enrolled in this session.</p>
        @endif
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('enrollModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="enrollForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Enroll Selected</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function toggleAllEnroll() {
    document.querySelectorAll('.enroll-checkbox').forEach(function (cb) {
        cb.checked = !cb.checked;
    });
}
</script>
@endsection
