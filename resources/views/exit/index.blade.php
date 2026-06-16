@extends('layouts.app')

@section('title', 'Exit Management - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Exit Management</h1>
            <p class="text-gray-600 mt-2">Manage employee exit processes and checklists</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button type="button" onclick="document.getElementById('newExitModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Initiate Exit
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="log-out" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $exitCases->total() }}</h3>
            <p class="text-gray-600 text-sm">Total Exits</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $exitCases->where('status', 'initiated')->count() }}</h3>
            <p class="text-gray-600 text-sm">In Progress</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $exitCases->where('status', 'pending_clearance')->count() }}</h3>
            <p class="text-gray-600 text-sm">Pending Clearance</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $exitCases->where('status', 'completed')->count() }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
    </div>

    <!-- Exit Cases Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Exit Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($exitCases as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case->exit_number }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $case->exit_type)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $case->exit_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($case->status == 'initiated') bg-yellow-100 text-yellow-800
                                @elseif($case->status == 'pending_clearance') bg-blue-100 text-blue-800
                                @elseif($case->status == 'completed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">No exit cases found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $exitCases->links() }}
        </div>
    </div>

    <!-- New Exit Modal -->
    <div id="newExitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Initiate Exit Process</h3>
                    <button type="button" onclick="document.getElementById('newExitModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('exit.store') }}" method="POST" class="p-6">
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
                        <label for="exit_type" class="block text-sm font-medium text-gray-700">Exit Type</label>
                        <select id="exit_type" name="exit_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="resignation">Resignation</option>
                            <option value="retirement">Retirement</option>
                            <option value="mutual_separation">Mutual Separation</option>
                            <option value="misconduct_termination">Termination (Misconduct)</option>
                            <option value="retrenchment">Retrenchment</option>
                            <option value="death_in_service">Death in Service</option>
                        </select>
                    </div>
                    <div>
                        <label for="exit_date" class="block text-sm font-medium text-gray-700">Exit Date</label>
                        <input type="date" id="exit_date" name="exit_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('newExitModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Initiate Exit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
@endsection
