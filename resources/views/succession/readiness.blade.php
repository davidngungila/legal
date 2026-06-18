@extends('layouts.app')

@section('title', 'Readiness - Succession Planning')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Readiness Assessment</h1>
            <p class="text-gray-600 mt-2">Evaluate employee readiness for key roles</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <select class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option>All Departments</option>
                <option>IT</option>
                <option>HR</option>
                <option>Finance</option>
            </select>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Export Report
            </button>
        </div>
    </div>

    <!-- Readiness Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">8</div>
            <p class="text-green-100">Ready Now</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">15</div>
            <p class="text-yellow-100">1-2 Years</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">12</div>
            <p class="text-blue-100">2-3 Years</p>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">7</div>
            <p class="text-gray-100">Development Needed</p>
        </div>
    </div>

    <!-- Readiness Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Readiness Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readiness</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Development Needs</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'Sarah Williams', 'current' => 'Senior HR Manager', 'target' => 'HR Director', 'readiness' => 'Ready Now', 'needs' => 'Executive exposure'],
                        ['name' => 'John Doe', 'current' => 'IT Lead', 'target' => 'Head of IT', 'readiness' => '1-2 Years', 'needs' => 'Leadership training'],
                        ['name' => 'Amina Juma', 'current' => 'Finance Analyst', 'target' => 'Finance Manager', 'readiness' => '2-3 Years', 'needs' => 'Strategic planning'],
                        ['name' => 'David Kimani', 'current' => 'Sales Manager', 'target' => 'Head of Sales', 'readiness' => '1-2 Years', 'needs' => 'P&L responsibility']
                    ] as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-bold">{{ substr($employee['name'], 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $employee['current'] }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $employee['target'] }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($employee['readiness'] == 'Ready Now')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Ready Now</span>
                            @elseif($employee['readiness'] == '1-2 Years')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">1-2 Years</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">2-3 Years</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $employee['needs'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
