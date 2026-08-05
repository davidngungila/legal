@extends('layouts.app')

@section('title', 'Performance Analytics')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Performance Analytics</h1>
            <p class="text-gray-600 mt-2">Insights from appraisals, ratings and goal completion</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('performance.analytics') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cycle</label>
                <select name="cycle_id" class="form-select rounded-md border-gray-300">
                    <option value="">All Cycles</option>
                    @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}" {{ $cycleId == $cycle->id ? 'selected' : '' }}>{{ $cycle->cycle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Apply</button>
            </div>
            <div>
                <a href="{{ route('performance.analytics') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="file-text" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $reviews->count() }}</h3>
            <p class="text-gray-600 text-sm">Total Reviews</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="star" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ round($reviews->avg('rating') ?? 0, 2) }}</h3>
            <p class="text-gray-600 text-sm">Average Rating (0-5)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="check-circle" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</h3>
            <p class="text-gray-600 text-sm">Scoring Completion</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="award" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $topPerformers->count() }}</h3>
            <p class="text-gray-600 text-sm">Top Performers (4+)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Distribution</h3>
            <canvas id="distributionChart" height="120"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Average Rating by Cycle</h3>
            <canvas id="cycleTrendChart" height="120"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Average Rating by Department</h3>
            <canvas id="departmentChart" height="120"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Status Breakdown</h3>
            <canvas id="statusChart" height="120"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performers</h3>
            <div class="space-y-3">
                @forelse($topPerformers as $performer)
                <div class="flex items-center justify-between border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-medium">{{ substr($performer->employee?->first_name ?? 'E', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $performer->employee?->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $performer->employee?->department ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-green-600">{{ $performer->rating }}/5</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No top performers yet.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>
            <div class="space-y-3">
                @forelse($reviews->take(6) as $review)
                <div class="flex items-center justify-between border border-gray-200 rounded-lg p-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $review->employee?->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $review->review_date?->format('Y-m-d') }} • {{ $review->cycle?->cycle_name ?? 'No cycle' }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if(in_array($review->status, ['completed', 'finalized'])) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                        </span>
                        <span class="text-sm font-bold text-gray-900">{{ $review->rating }}/5</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No reviews yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }
    Chart.helpers.each(Chart.instances, function (instance) { instance.destroy(); });

    const distributionCtx = document.getElementById('distributionChart');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'bar',
            data: {
                labels: ['5 - Outstanding', '4 - Excellent', '3 - Good', '2 - Needs Improvement', '1 - Poor'],
                datasets: [{
                    label: 'Reviews',
                    data: [{{ $distribution[5] }}, {{ $distribution[4] }}, {{ $distribution[3] }}, {{ $distribution[2] }}, {{ $distribution[1] }}],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#f97316', '#ef4444'],
                    borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    const trendCtx = document.getElementById('cycleTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($cycleTrend->pluck('name')),
                datasets: [{
                    label: 'Avg Rating',
                    data: @json($cycleTrend->pluck('avg')),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 5 } } }
        });
    }

    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: @json($departments->pluck('department')),
                datasets: [{
                    label: 'Avg Rating',
                    data: @json($departments->pluck('avg')),
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 5 } } }
        });
    }

    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statuses->keys()->map(fn ($s) => ucfirst(str_replace('_', ' ', $s)))->values()),
                datasets: [{
                    data: @json($statuses->values()),
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#f97316', '#ef4444', '#8b5cf6']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>
@endsection
