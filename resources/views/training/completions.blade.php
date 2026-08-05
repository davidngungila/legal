@extends('layouts.app')

@section('title', 'Training Completions - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training Completions</h1>
            <p class="text-gray-600 mt-2">Completed trainings, assessments and certificates</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total Completions</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-green-600">{{ $stats['passed'] }}</h3>
            <p class="text-gray-600 text-sm">Passed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-purple-600">{{ $stats['certified'] }}</h3>
            <p class="text-gray-600 text-sm">Certified</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-indigo-600">{{ $stats['avg_score'] }}%</h3>
            <p class="text-gray-600 text-sm">Average Score</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('training.completions') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input rounded-md border-gray-300" placeholder="Name or employee ID...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select name="program_id" class="form-select rounded-md border-gray-300">
                    <option value="">All Programs</option>
                    @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('training.completions') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if($completions->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($completions as $completion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <span class="text-indigo-700 font-semibold text-sm">{{ strtoupper(substr($completion->employee?->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($completion->employee?->last_name ?? '', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $completion->employee?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $completion->employee?->employee_id }} · {{ $completion->employee?->department ?: '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $completion->session?->program?->name ?? '—' }}</div>
                            @if($completion->session?->program?->is_certification)
                            <span class="inline-flex items-center px-1.5 py-0.5 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                <i data-feather="award" class="w-3 h-3 mr-0.5"></i> Certification
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $completion->session?->title ?: '—' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $completion->completed_at ? $completion->completed_at->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-4">
                            @if($completion->assessment_score !== null)
                            <div class="text-sm font-semibold {{ $completion->passed ? 'text-green-600' : 'text-red-600' }}">{{ $completion->assessment_score }}%</div>
                            @else
                            <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($completion->passed)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Passed</span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Completed</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('training.certificate', $completion->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <i data-feather="download" class="w-4 h-4 mr-1"></i> Download
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $completions->links() }}
        </div>
        @else
        <div class="text-center text-gray-500 py-12">No completed trainings yet.</div>
        @endif
    </div>
</div>
@endsection
