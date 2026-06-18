@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Leave Balances</h1>
            <p class="text-gray-600 mt-2">View and manage employee leave balances</p>
        </div>
    </div>

    <!-- Employee Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('leave.balances') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Employee
                    </label>
                    <select name="employee_id" id="employeeSelect" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="this.form.submit()">
                        <option value="">-- Select an employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if($employee && $leaveEntitlements)
        <!-- Employee Info -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 mb-6 text-white">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-2xl font-bold text-indigo-600">
                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                    <p class="text-indigo-100">{{ $employee->department ?? 'No Department' }}</p>
                </div>
            </div>
        </div>

        <!-- Leave Balance Cards -->
        @if($leaveEntitlements->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($leaveEntitlements as $entitlement)
                    @php
                        $remaining = $entitlement->entitlement_days - $entitlement->taken_days;
                        $progress = $entitlement->entitlement_days > 0 ? ($entitlement->taken_days / $entitlement->entitlement_days) * 100 : 0;
                        $progressClass = $progress > 75 ? 'bg-red-500' : ($progress > 50 ? 'bg-yellow-500' : 'bg-green-500');
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $entitlement->leaveType->type_name ?? 'Unknown Type' }}</h3>
                                <p class="text-sm text-gray-500">Current Cycle</p>
                            </div>
                            <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $entitlement->leaveType->is_paid ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                                <i data-feather="calendar" class="w-6 h-6"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Entitled:</span>
                                <span class="font-semibold text-gray-900">{{ $entitlement->entitlement_days }} days</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Taken:</span>
                                <span class="font-semibold text-orange-600">{{ $entitlement->taken_days }} days</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Remaining:</span>
                                <span class="font-semibold text-green-600">{{ $remaining }} days</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Usage Progress</span>
                                <span>{{ round($progress) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $progressClass }} h-2 rounded-full transition-all duration-300" style="width: {{ min($progress, 100) }}%"></div>
                            </div>
                        </div>

                        @if($entitlement->cycle_start && $entitlement->cycle_end)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500">
                                    Valid from: {{ \Carbon\Carbon::parse($entitlement->cycle_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($entitlement->cycle_end)->format('M d, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Balances -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="clipboard" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leave Balances Found</h3>
                <p class="text-gray-500">This employee hasn't been assigned any leave entitlements yet.</p>
            </div>
        @endif
    @else
        <!-- Select Employee Prompt -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="users" class="w-10 h-10 text-indigo-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Select an Employee</h3>
            <p class="text-gray-500">Choose an employee from the dropdown above to view their leave balances.</p>
        </div>
    @endif
</div>
@endsection