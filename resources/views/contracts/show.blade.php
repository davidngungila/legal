@extends('layouts.app')

@section('title', 'Contract Details - ' . $contract->formatted_contract_number)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Contract Details</h1>
            <p class="text-gray-600 mt-2">Viewing contract {{ $contract->formatted_contract_number }} for {{ $contract->employee->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('contracts.edit', $contract->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>
                Edit Contract
            </a>
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Contract Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex justify-between items-start mb-8 pb-8 border-b border-gray-100">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Employment Contract</h2>
                        <p class="text-gray-500">LegalHR Tanzania - Standard Agreement</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-{{ $contract->status_badge_color }}-100 text-{{ $contract->status_badge_color }}-700 rounded-full text-sm font-medium capitalize">
                            {{ $contract->status }}
                        </span>
                        <p class="text-xs text-gray-400 mt-2">Number: {{ $contract->formatted_contract_number }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Employee Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-500">Full Name</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Position</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee->position }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Department</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee->department }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Contract Terms</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-500">Contract Type</label>
                                <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $contract->contract_type) }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Period</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $contract->start_date->format('d M, Y') }} - 
                                    {{ $contract->end_date ? $contract->end_date->format('d M, Y') : 'Open-ended' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Salary</label>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->formatted_salary }} ({{ $contract->payment_frequency }})</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Job Description</h3>
                    <div class="bg-gray-50 rounded-lg p-6 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $contract->job_description ?: 'No specific job description provided.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions/Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Contract Status</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Signed Date</span>
                        <span class="text-sm font-medium">{{ $contract->signed_at ? $contract->signed_at->format('d M, Y') : 'Not Signed' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Auto Renewal</span>
                        <span class="text-sm font-medium">{{ $contract->auto_renewal ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
                
                @if($contract->status === 'pending_signature')
                    <div class="mt-6">
                        <button class="w-full py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition-colors">
                            Sign Contract
                        </button>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i data-feather="download" class="w-4 h-4 mr-2"></i> Download PDF
                    </button>
                    <button class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print Contract
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
