@extends('layouts.app')

@section('title', 'Investigations - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Investigations</h1>
            <p class="text-gray-600 mt-2">Manage disciplinary case investigations</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button type="button" onclick="openModal('newCaseModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Report Case
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="search" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</h3>
            <p class="text-gray-600 text-sm">Active Investigations</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['reported']) }}</h3>
            <p class="text-gray-600 text-sm">Awaiting Investigation</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total On This Page</p>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Investigation Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incident Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investigator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cases as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case->case_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ strtoupper(substr($case->employee->first_name ?? 'E', 0, 1)) }}{{ strtoupper(substr($case->employee->last_name ?? 'E', 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $case->employee->first_name }} {{ $case->employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $case->employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $case->case_type === 'major' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($case->case_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $case->incident_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $case->investigator ?: 'Not assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($case->status == 'investigating') bg-blue-100 text-blue-800
                                @elseif($case->status == 'reported') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="openModal('detailModal{{ $case->id }}')" class="text-indigo-600 hover:text-indigo-900" title="View details">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                                @if($case->status === 'reported')
                                <button onclick="openModal('startModal{{ $case->id }}')" class="text-blue-600 hover:text-blue-900" title="Start investigation">
                                    <i data-feather="play" class="w-4 h-4"></i>
                                </button>
                                @else
                                <button onclick="openModal('findingsModal{{ $case->id }}')" class="text-purple-600 hover:text-purple-900" title="Record findings">
                                    <i data-feather="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openModal('hearingModal{{ $case->id }}')" class="text-green-600 hover:text-green-900" title="Schedule hearing">
                                    <i data-feather="calendar" class="w-4 h-4"></i>
                                </button>
                                @endif
                                <button onclick="deleteInvestigation({{ $case->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="search" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No investigation cases found</p>
                                <p class="text-sm text-gray-600 mt-2">Report a case to begin the investigation workflow</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $cases->links() }}
        </div>
    </div>
</div>

<!-- Report New Case Modal -->
<x-advanced-modal id="newCaseModal" title="Report Disciplinary Case" description="Open a new case to begin the investigation workflow." icon="plus" color="indigo" size="lg">
    <form action="{{ route('discipline.store') }}" method="POST" id="newCaseForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Case Type <span class="text-red-500">*</span></label>
                <select name="case_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="minor">Minor</option>
                    <option value="major">Major</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Incident Date <span class="text-red-500">*</span></label>
                <input type="date" name="incident_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Incident Description <span class="text-red-500">*</span></label>
                <textarea name="incident_description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Describe the incident..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newCaseModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newCaseForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Report Case</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Per-case modals -->
@foreach($cases as $case)
<!-- Start Investigation -->
@if($case->status === 'reported')
<x-advanced-modal id="startModal{{ $case->id }}" title="Start Investigation" description="Assign an investigator to begin the investigation for {{ $case->case_number }}." icon="play" color="blue" size="lg">
    <form action="{{ route('discipline.investigations.start', $case->id) }}" method="POST" id="startForm{{ $case->id }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Investigator <span class="text-red-500">*</span></label>
                <input type="text" name="investigator" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., HR Manager">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Investigation Start Date</label>
                <input type="date" name="investigation_started_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('startModal{{ $case->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="startForm{{ $case->id }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Start Investigation</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@else
<!-- Record Findings -->
<x-advanced-modal id="findingsModal{{ $case->id }}" title="Investigation Findings" description="Record findings and recommendation for {{ $case->case_number }}." icon="edit" color="purple" size="lg">
    <form action="{{ route('discipline.investigations.update', $case->id) }}" method="POST" id="findingsForm{{ $case->id }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Investigator</label>
                <input type="text" name="investigator" value="{{ $case->investigator }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Findings</label>
                <textarea name="investigation_findings" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Summary of evidence gathered and findings...">{{ $case->investigation_findings }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Recommendation</label>
                <textarea name="recommendation" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Recommended action...">{{ $case->recommendation }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Case Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="investigating" {{ $case->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ $case->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="hearing">Move to Hearing</option>
                    <option value="reported">Back to Reported</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('findingsModal{{ $case->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="findingsForm{{ $case->id }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">Save Findings</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Schedule Hearing -->
<x-advanced-modal id="hearingModal{{ $case->id }}" title="Schedule Hearing" description="Schedule a disciplinary hearing for {{ $case->case_number }}." icon="calendar" color="green" size="lg">
    <form action="{{ route('discipline.investigations.hearing', $case->id) }}" method="POST" id="hearingForm{{ $case->id }}">
        @csrf
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hearing Date <span class="text-red-500">*</span></label>
                    <input type="date" name="hearing_date" value="{{ $case->hearing?->hearing_date?->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Time</label>
                    <input type="time" name="hearing_time" value="{{ $case->hearing?->hearing_time?->format('H:i') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Venue</label>
                <input type="text" name="venue" value="{{ $case->hearing?->venue }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Conference Room 2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Committee Members</label>
                <input type="text" name="committee_members" value="{{ $case->hearing?->committee_members }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Comma separated names">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Employee Representative</label>
                <input type="text" name="employee_representative" value="{{ $case->hearing?->employee_representative }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('hearingModal{{ $case->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="hearingForm{{ $case->id }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Schedule Hearing</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endif

<!-- Detail Modal -->
<x-advanced-modal id="detailModal{{ $case->id }}" title="Case: {{ $case->case_number }}" description="Full case and investigation details." icon="eye" color="indigo" size="2xl">
    <div class="space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs text-gray-500">Employee</label>
                <p class="text-sm font-medium text-gray-900">{{ $case->employee->first_name }} {{ $case->employee->last_name }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Case Type</label>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ $case->case_type }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Incident Date</label>
                <p class="text-sm font-medium text-gray-900">{{ $case->incident_date->format('M d, Y') }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Status</label>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $case->status) }}</p>
            </div>
        </div>
        <div>
            <label class="block text-xs text-gray-500">Reported By</label>
            <p class="text-sm text-gray-700">{{ $case->reporter->name ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-xs text-gray-500">Incident Description</label>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $case->incident_description }}</p>
        </div>
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Investigation</h4>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs text-gray-500">Investigator</label>
                    <p class="text-sm text-gray-700">{{ $case->investigator ?: 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Started</label>
                    <p class="text-sm text-gray-700">{{ $case->investigation_started_at?->format('M d, Y') ?? '-' }}</p>
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs text-gray-500">Findings</label>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $case->investigation_findings ?: 'No findings recorded yet.' }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Recommendation</label>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $case->recommendation ?: 'No recommendation yet.' }}</p>
            </div>
        </div>
        @if($case->hearing)
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Scheduled Hearing</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-500">Date</label>
                    <p class="text-sm text-gray-700">{{ $case->hearing->hearing_date?->format('M d, Y') ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Time</label>
                    <p class="text-sm text-gray-700">{{ $case->hearing->hearing_time?->format('H:i') ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Venue</label>
                    <p class="text-sm text-gray-700">{{ $case->hearing->venue ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-advanced-modal>
@endforeach

<script>
function deleteInvestigation(id) {
    if (confirm('Are you sure you want to delete this case? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/discipline/${id}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

feather.replace();
</script>
@endsection
