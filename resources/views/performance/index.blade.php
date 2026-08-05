@extends('layouts.app')

@section('title', 'Performance Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Performance Management</h1>
            <p class="text-gray-600 mt-2">Track employee performance and manage reviews</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="exportPerformanceReport()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button onclick="openModal('newReviewModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Review
            </button>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ round($avgRating * 20) }}%</h3>
            <p class="text-gray-600 text-sm">Average Performance</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $completedReviews }}</h3>
            <p class="text-gray-600 text-sm">Completed Reviews</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $pendingReviews }}</h3>
            <p class="text-gray-600 text-sm">Pending Reviews</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $topPerformers->count() }}</h3>
            <p class="text-gray-600 text-sm">Top Performers</p>
        </div>
    </div>

    <!-- Performance Analytics Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center space-x-2">
                <label for="filterPeriod" class="text-sm font-medium text-gray-700">Period:</label>
                <select id="filterPeriod" onchange="applyFilters()" class="form-select">
                    <option value="quarter" {{ $filterPeriod == 'quarter' ? 'selected' : '' }}>Last Quarter</option>
                    <option value="sixmonths" {{ $filterPeriod == 'sixmonths' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="year" {{ $filterPeriod == 'year' ? 'selected' : '' }}>Last Year</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label for="filterDepartment" class="text-sm font-medium text-gray-700">Department:</label>
                <select id="filterDepartment" onchange="applyFilters()" class="form-select">
                    <option value="all" {{ $filterDepartment == 'all' ? 'selected' : '' }}>All Departments</option>
                    <option value="hr" {{ $filterDepartment == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="it" {{ $filterDepartment == 'it' ? 'selected' : '' }}>IT</option>
                    <option value="finance" {{ $filterDepartment == 'finance' ? 'selected' : '' }}>Finance</option>
                    <option value="sales" {{ $filterDepartment == 'sales' ? 'selected' : '' }}>Sales</option>
                    <option value="operations" {{ $filterDepartment == 'operations' ? 'selected' : '' }}>Operations</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Performance Reviews Schedule -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Performance Reviews Schedule</h3>
            <div class="flex space-x-3">
                <select class="form-select">
                    <option>Q1 2024</option>
                    <option>Q2 2024</option>
                    <option>Q3 2024</option>
                    <option>Q4 2024</option>
                </select>
                <button onclick="openModal('newReviewModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="calendar" class="w-4 h-4 inline mr-2"></i>
                    Schedule Review
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($reviews as $review)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($review->status === 'completed') bg-green-100 text-green-800
                        @elseif($review->status === 'in_progress') bg-yellow-100 text-yellow-800
                        @elseif($review->status === 'scheduled') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $review->review_date?->format('Y-m-d') }}</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-medium">{{ substr($review->employee?->first_name ?? 'E', 0, 1) }}{{ substr($review->employee?->last_name ?? 'E', 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-gray-900">{{ $review->employee?->first_name }} {{ $review->employee?->last_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $review->employee?->job_title ?? 'Employee' }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 mb-3">{{ $review->comments ?? 'Performance Review' }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        @if($review->status === 'completed')
                        <span class="text-sm font-medium text-green-600">Score: {{ $review->rating * 20 }}%</span>
                        @endif
                    </div>
                    <button onclick="viewReview({{ $review->id }})" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View →</button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-12">No reviews scheduled yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Top Performers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Top Performers This Quarter</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($topPerformers as $performer)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr($performer->employee?->first_name ?? 'E', 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">{{ $performer->employee?->first_name }} {{ $performer->employee?->last_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $performer->employee?->job_title ?? 'Employee' }}</p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $performer->rating * 20 }}%</div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Department:</span>
                        <span class="font-medium">{{ $performer->employee?->department ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Performance:</span>
                        <span class="font-medium text-green-600">{{ $performer->rating >= 4 ? 'Outstanding' : 'Excellent' }}</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Profile →</button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-12">No top performers yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Performance Analytics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Performance Analytics</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Performance Distribution</h4>
                <div class="space-y-3">
                    @php
                    $ratingCounts = [
                        'Outstanding (90-100%)' => $reviews->where('rating', 5)->count(),
                        'Excellent (80-89%)' => $reviews->where('rating', 4)->count(),
                        'Good (70-79%)' => $reviews->where('rating', 3)->count(),
                        'Needs Improvement (60-69%)' => $reviews->where('rating', 2)->count(),
                        'Poor (Below 60%)' => $reviews->where('rating', 1)->count()
                    ];
                    $totalReviewsForDistribution = $reviews->count() ?: 1;
                    @endphp
                    @foreach($ratingCounts as $ratingLabel => $ratingCount)
                    @php
                    $ratingColors = [
                        'Outstanding (90-100%)' => 'green',
                        'Excellent (80-89%)' => 'blue',
                        'Good (70-79%)' => 'yellow',
                        'Needs Improvement (60-69%)' => 'orange',
                        'Poor (Below 60%)' => 'red'
                    ];
                    $color = $ratingColors[$ratingLabel] ?? 'gray';
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-{{ $color }}-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">{{ $ratingLabel }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $ratingCount }}</span>
                            <span class="text-sm text-gray-500">({{ round(($ratingCount / $totalReviewsForDistribution) * 100) }}%)</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Improvement Areas</h4>
                <div class="space-y-3">
                    @foreach([
                        ['area' => 'Time Management', 'employees' => 23, 'focus' => 'High'],
                        ['area' => 'Communication Skills', 'employees' => 18, 'focus' => 'Medium'],
                        ['area' => 'Technical Skills', 'employees' => 15, 'focus' => 'High'],
                        ['area' => 'Leadership', 'employees' => 12, 'focus' => 'Medium'],
                        ['area' => 'Project Management', 'employees' => 8, 'focus' => 'Low']
                    ] as $area)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $area['area'] }}</p>
                            <p class="text-xs text-gray-500">{{ $area['employees'] }} employees</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($area['focus'] === 'High') bg-red-100 text-red-800
                            @elseif($area['focus'] === 'Medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ $area['focus'] }} Priority
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Review Modal -->
<x-advanced-modal id="newReviewModal" title="Schedule New Review" icon="plus" color="indigo" size="lg">
    <form action="{{ route('performance.store') }}" method="POST" id="newReviewForm">
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
                    <label for="review_date" class="block text-sm font-medium text-gray-700">Review Date</label>
                    <input type="date" id="review_date" name="review_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700">Initial Rating</label>
                    <select id="rating" name="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Needs Improvement</option>
                        <option value="3" selected>3 - Good</option>
                        <option value="4">4 - Excellent</option>
                        <option value="5">5 - Outstanding</option>
                    </select>
                </div>
                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700">Review Comments</label>
                    <textarea id="comments" name="comments" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                </div>
        </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newReviewModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newReviewForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Schedule Review</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
@php
    $exportRows = $reviews->map(fn($r) => [
        'Employee' => ($r->employee?->first_name ?? '') . ' ' . ($r->employee?->last_name ?? ''),
        'Title' => $r->employee?->job_title ?? '',
        'Status' => ucfirst(str_replace('_', ' ', $r->status)),
        'Date' => $r->review_date?->format('Y-m-d') ?? '',
        'Rating' => $r->rating,
        'Score' => $r->status === 'completed' ? $r->rating * 20 : '',
        'Comments' => $r->comments ?? '',
    ])->values();
@endphp
function applyFilters() {
    const period = document.getElementById('filterPeriod').value;
    const dept = document.getElementById('filterDepartment').value;
    window.location.href = `{{ route('performance.index') }}?period=${period}&department=${dept}`;
}

function viewReview(id) {
    window.location.href = '{{ route('performance.show', ['review' => ':id']) }}'.replace(':id', id);
}

function exportPerformanceReport() {
    const reviews = @json($exportRows);

    if (!reviews.length) {
        alert('No performance reviews to export.');
        return;
    }

    const headers = Object.keys(reviews[0]);
    const escapeCsv = (value) => `"${String(value ?? '').replace(/"/g, '""')}"`;
    const csv = [
        headers.join(','),
        ...reviews.map(row => headers.map(h => escapeCsv(row[h])).join(','))
    ].join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'performance-report.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection
