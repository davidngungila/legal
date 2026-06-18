@extends('layouts.app')

@section('title', 'Career Paths - Succession Planning')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Career Paths</h1>
            <p class="text-gray-600 mt-2">Visualize career progression and development opportunities</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            Create Career Path
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Career Path List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Active Career Paths</h3>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'HR Career Track', 'levels' => 5, 'employees' => 12],
                    ['name' => 'IT Technical Track', 'levels' => 4, 'employees' => 18],
                    ['name' => 'Finance Track', 'levels' => 5, 'employees' => 14],
                    ['name' => 'Sales Management', 'levels' => 4, 'employees' => 10]
                ] as $path)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-1">{{ $path['name'] }}</h4>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ $path['levels'] }} levels</span>
                        <span>{{ $path['employees'] }} employees</span>
                    </div>
                    <button class="mt-2 text-sm text-indigo-600 font-medium hover:underline">View Path</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Career Path Example -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">HR Career Track</h3>
            <div class="space-y-4">
                @foreach([
                    ['level' => 'Level 1', 'title' => 'HR Assistant', 'time' => '1-2 years'],
                    ['level' => 'Level 2', 'title' => 'HR Officer', 'time' => '2-3 years'],
                    ['level' => 'Level 3', 'title' => 'HR Manager', 'time' => '3-4 years'],
                    ['level' => 'Level 4', 'title' => 'Senior HR Manager', 'time' => '4-5 years'],
                    ['level' => 'Level 5', 'title' => 'HR Director', 'time' => '5+ years']
                ] as $level)
                <div class="flex items-center space-x-4 p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-100">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-indigo-600 font-bold text-sm">{{ substr($level['level'], -1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $level['title'] }}</p>
                        <p class="text-xs text-gray-600">Typical time: {{ $level['time'] }}</p>
                    </div>
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
