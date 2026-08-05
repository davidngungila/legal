@extends('layouts.app')

@section('title', 'Appraisal #' . $review->id . ' - Performance Management')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <a href="{{ route('performance.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1"></i> Back to Appraisals
            </a>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Appraisal Review</h1>
            <p class="text-gray-600 mt-2">
                {{ $review->employee?->full_name }} • {{ $review->review_date?->format('Y-m-d') }} • {{ $review->cycle?->cycle_name ?? 'No cycle' }}
            </p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if(in_array($review->status, ['completed', 'finalized'])) bg-green-100 text-green-800
                @elseif($review->status === 'submitted') bg-yellow-100 text-yellow-800
                @elseif($review->status === 'scheduled') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst(str_replace('_', ' ', $review->status)) }}
            </span>
            <form action="{{ route('performance.update', $review->id) }}" method="POST">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()" class="form-select rounded-md border-gray-300">
                    <option value="draft" {{ $review->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ $review->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="finalized" {{ $review->status == 'finalized' ? 'selected' : '' }}>Finalized</option>
                    <option value="completed" {{ $review->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="scheduled" {{ $review->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="pending" {{ $review->status == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </form>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="star" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $review->final_rating !== null ? $review->final_rating . ' / 5' : '—' }}</h3>
            <p class="text-gray-600 text-sm">Final Rating</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="user" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $review->self_rating !== null ? $review->self_rating . ' / 5' : '—' }}</h3>
            <p class="text-gray-600 text-sm">Self Rating</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="user-check" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $review->supervisor_rating !== null ? $review->supervisor_rating . ' / 5' : '—' }}</h3>
            <p class="text-gray-600 text-sm">Supervisor Rating</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="settings" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $review->calibrated_rating !== null ? $review->calibrated_rating . ' / 5' : '—' }}</h3>
            <p class="text-gray-600 text-sm">Calibrated Rating</p>
        </div>
    </div>

    @if($review->comments)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Review Comments</h3>
        <p class="text-gray-700">{{ $review->comments }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">KPI Scoring</h3>
            <button onclick="submitScores()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                Save Scores
            </button>
        </div>

        @forelse($goals as $goal)
        <div class="mb-6 last:mb-0">
            <h4 class="font-semibold text-gray-900 mb-3">{{ $goal->goal_title }}</h4>
            @forelse($goal->kpis as $kpi)
            @php
                $rating = $review->appraisalRatings->firstWhere('kpi_id', $kpi->id);
            @endphp
            <div class="border border-gray-200 rounded-lg p-4 mb-3">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $kpi->kpi_description }}</p>
                        <p class="text-xs text-gray-500">
                            Target: {{ $kpi->target ?: '—' }} ({{ $kpi->measurement_unit ?: 'unit' }}) • Weight: {{ $kpi->weight }}% • Deadline: {{ $kpi->deadline?->format('Y-m-d') ?: '—' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Self Score (0-5)</label>
                        <input type="number" min="0" max="5" step="0.1" value="{{ $rating?->self_score ?? '' }}" name="kpis[{{ $kpi->id }}][self_score]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Supervisor Score (0-5)</label>
                        <input type="number" min="0" max="5" step="0.1" value="{{ $rating?->supervisor_score ?? '' }}" name="kpis[{{ $kpi->id }}][supervisor_score]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Calibrated Score (0-5)</label>
                        <input type="number" min="0" max="5" step="0.1" value="{{ $rating?->calibrated_score ?? '' }}" name="kpis[{{ $kpi->id }}][calibrated_score]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.0">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Comments</label>
                        <textarea name="kpis[{{ $kpi->id }}][comments]" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Comments on this KPI">{{ $rating?->comments ?? '' }}</textarea>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">No KPIs linked to this goal.</p>
            @endforelse
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            <p>No goals/KPIs found for this employee in the current cycle.</p>
            <p class="text-sm mt-2">Link goals to this employee and cycle (Performance → Goals) before scoring.</p>
        </div>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recorded Scores</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KPI</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Self</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calibrated</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($review->appraisalRatings as $rating)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-900">{{ $rating->kpi?->kpi_description ?? 'KPI #' . $rating->kpi_id }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ $rating->self_score ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ $rating->supervisor_score ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ $rating->calibrated_score ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $rating->comments ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No scores recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function submitScores() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('performance.ratings.store', $review->id) }}';
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    document.querySelectorAll('input[name^="kpis["], textarea[name^="kpis["]').forEach(function(el) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = el.name;
        hidden.value = el.value;
        form.appendChild(hidden);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
