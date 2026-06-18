@extends('layouts.app')

@section('title', 'Talent Pools - Succession Planning')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Talent Pools</h1>
            <p class="text-gray-600 mt-2">Identify and develop future leaders</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
            Create Talent Pool
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- High Potentials -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-3xl font-bold text-blue-600">12</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">High Potentials</h3>
            <p class="text-sm text-gray-600 mb-4">Top 10% performers ready for leadership roles</p>
            <button class="text-sm text-indigo-600 font-medium hover:underline">View All</button>
        </div>

        <!-- Future Leaders -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-3xl font-bold text-purple-600">18</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Future Leaders</h3>
            <p class="text-sm text-gray-600 mb-4">Employees ready for advancement in 1-2 years</p>
            <button class="text-sm text-indigo-600 font-medium hover:underline">View All</button>
        </div>

        <!-- Key Roles Coverage -->
        <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl border border-green-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="target" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-3xl font-bold text-green-600">85%</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Key Roles Coverage</h3>
            <p class="text-sm text-gray-600 mb-4">Critical positions with ready successors</p>
            <button class="text-sm text-indigo-600 font-medium hover:underline">View Details</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Talent Pool List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Talent Pools</h3>
            <div class="space-y-3">
                @foreach([
                    ['name' => 'Executive Leadership', 'count' => 5, 'ready' => 2],
                    ['name' => 'Senior Management', 'count' => 8, 'ready' => 4],
                    ['name' => 'Department Heads', 'count' => 12, 'ready' => 6],
                    ['name' => 'Technical Specialists', 'count' => 10, 'ready' => 5],
                    ['name' => 'Emerging Leaders', 'count' => 15, 'ready' => 3]
                ] as $pool)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ $pool['name'] }}</h4>
                        <span class="text-sm text-gray-600">{{ $pool['count'] }} employees</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-green-600">{{ $pool['ready'] }} ready now</span>
                        <button class="text-sm text-indigo-600 font-medium hover:underline">Manage</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Additions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Recent Additions to Talent Pools</h3>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'Sarah Williams', 'role' => 'Senior HR Manager', 'pool' => 'Executive Leadership'],
                    ['name' => 'John Doe', 'role' => 'IT Lead', 'pool' => 'Technical Specialists'],
                    ['name' => 'Amina Juma', 'role' => 'Finance Analyst', 'pool' => 'Emerging Leaders'],
                    ['name' => 'David Kimani', 'role' => 'Sales Manager', 'pool' => 'Senior Management']
                ] as $employee)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">{{ substr($employee['name'], 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</p>
                        <p class="text-xs text-gray-600">{{ $employee['role'] }} • {{ $employee['pool'] }}</p>
                    </div>
                    <button class="text-sm text-indigo-600 font-medium hover:underline">View</button>
                </div>
                @endforeach
            </div>
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
