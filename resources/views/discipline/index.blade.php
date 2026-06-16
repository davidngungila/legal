@extends('layouts.app')

@section('title', 'Disciplinary Management - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Disciplinary Management</h1>
            <p class="text-gray-600 mt-2">Manage disciplinary cases and warnings</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button type="button" onclick="document.getElementById('newCaseModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Case
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $cases->total() }}</h3>
            <p class="text-gray-600 text-sm">Total Cases</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $cases->where('status', 'reported')->count() }}</h3>
            <p class="text-gray-600 text-sm">Pending Cases</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $warnings->count() }}</h3>
            <p class="text-gray-600 text-sm">Active Warnings</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $cases->where('status', 'resolved')->count() }}</h3>
            <p class="text-gray-600 text-sm">Resolved Cases</p>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Disciplinary Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incident Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                    <span class="text-white text-xs font-medium">{{ substr($case->employee->first_name[0] ?? 'E', 0, 1) }}{{ substr($case->employee->last_name[0] ?? 'E', 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $case->employee->first_name }} {{ $case->employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $case->employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($case->case_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $case->incident_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($case->status == 'reported') bg-yellow-100 text-yellow-800
                                @elseif($case->status == 'investigating') bg-blue-100 text-blue-800
                                @elseif($case->status == 'resolved') bg-green-100 text-green-800
                                @elseif($case->status == 'hearing') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($case->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">No disciplinary cases found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $cases->links() }}
        </div>
    </div>

    <!-- New Case Modal -->
    <div id="newCaseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">New Disciplinary Case</h3>
                    <button type="button" onclick="document.getElementById('newCaseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('discipline.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                        <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="case_type" class="block text-sm font-medium text-gray-700">Case Type</label>
                        <select id="case_type" name="case_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="minor">Minor</option>
                            <option value="major">Major</option>
                        </select>
                    </div>
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700">Incident Date</label>
                        <input type="date" id="incident_date" name="incident_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="incident_description" class="block text-sm font-medium text-gray-700">Incident Description</label>
                        <textarea id="incident_description" name="incident_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('newCaseModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Open Case</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
@endsection
