@extends('layouts.app')

@section('title', 'Employee Training Records - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('induction-training.index') }}" class="hover:text-indigo-600">Induction Training</a>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
                <span>Training History</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training History: {{ $employee->first_name }} {{ $employee->last_name }}</h1>
            <p class="text-gray-600 mt-2">Comprehensive training and induction records for {{ $employee->employee_id }}</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.history.back()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back
            </button>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                Print History
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Column: Employee Summary -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                    <span class="text-blue-600 font-bold text-2xl">
                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $employee->position }}</p>
                
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3 text-left">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Total Trainings:</span>
                        <span class="font-bold text-gray-900">{{ $trainings->count() }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Total Hours:</span>
                        <span class="font-bold text-gray-900">{{ $trainings->sum('training_duration_hours') }}h</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Success Rate:</span>
                        @php
                            $passedCount = $trainings->where('assessment_passed', true)->count();
                            $rate = $trainings->count() > 0 ? round(($passedCount / $trainings->count()) * 100) : 0;
                        @endphp
                        <span class="font-bold text-green-600">{{ $rate }}%</span>
                    </div>
                </div>
            </div>

            <!-- Training Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Training by Type</h3>
                <div class="space-y-4">
                    @foreach(['company_policies', 'safety_procedures', 'job_specific', 'compliance', 'other'] as $type)
                        @php
                            $typeCount = $trainings->where('training_type', $type)->count();
                            $percentage = $trainings->count() > 0 ? ($typeCount / $trainings->count()) * 100 : 0;
                            $color = match($type) {
                                'company_policies' => 'bg-blue-500',
                                'safety_procedures' => 'bg-red-500',
                                'job_specific' => 'bg-green-500',
                                'compliance' => 'bg-purple-500',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                                <span class="font-medium">{{ $typeCount }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Training Timeline -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($trainings as $training)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row">
                        <!-- Left bar indicator -->
                        <div class="w-2 {{ $training->assessment_passed ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        
                        <div class="flex-1 p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                                <div>
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded">
                                            {{ str_replace('_', ' ', $training->training_type) }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($training->training_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $training->training_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Trainer: <span class="font-medium text-gray-700">{{ $training->trainer_name }}</span></p>
                                </div>
                                <div class="mt-4 md:mt-0 flex flex-col items-end">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs text-gray-500">Duration:</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $training->training_duration_hours }} Hours</span>
                                    </div>
                                    <span class="px-3 py-1 {{ $training->assessment_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                        {{ $training->assessment_passed ? 'Assessment Passed' : 'Assessment Failed' }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Description</h4>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $training->training_description }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Assessment Details</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Score:</span>
                                            <span class="font-bold text-gray-900">{{ $training->assessment_score ?? 'N/A' }}%</span>
                                        </div>
                                        @if($training->next_training_date)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Next Scheduled:</span>
                                            <span class="font-bold text-indigo-600">{{ \Carbon\Carbon::parse($training->next_training_date)->format('M d, Y') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end space-y-2">
                                    @if($training->training_materials_path)
                                        <a href="{{ Storage::url($training->training_materials_path) }}" target="_blank"
                                           class="flex items-center text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
                                            <i data-feather="download" class="w-4 h-4 mr-2"></i>
                                            Download Training Materials
                                        </a>
                                    @endif
                                    @if($training->completion_certificate_path)
                                        <a href="{{ Storage::url($training->completion_certificate_path) }}" target="_blank"
                                           class="flex items-center text-sm text-green-600 hover:text-green-800 transition-colors">
                                            <i data-feather="award" class="w-4 h-4 mr-2"></i>
                                            View Completion Certificate
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            @if($training->feedback_comments)
                                <div class="mt-4 p-3 border-l-4 border-indigo-100 bg-indigo-50/30 rounded-r-lg">
                                    <p class="text-xs italic text-gray-600">"{{ $training->feedback_comments }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-100 p-4 rounded-full">
                            <i data-feather="book-open" class="w-12 h-12 text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Training Records Found</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        This employee has no training history recorded in the system.
                    </p>
                    <a href="{{ route('induction-training.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Schedule New Training
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush