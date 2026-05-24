@extends('layouts.app')

@section('title', 'Edit Contract - ' . $contract->formatted_contract_number)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Contract</h1>
            <p class="text-gray-600 mt-2">Updating contract for {{ $contract->employee->full_name }}</p>
        </div>
        <div>
            <a href="{{ route('contracts.show', $contract->id) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </div>

    <form action="{{ route('contracts.update', $contract->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="client_id" value="{{ $currentClient->id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Contract Basics</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                            <select name="employee_id" id="employee_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('employee_id') border-red-500 @enderror">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Contract Type *</label>
                            <select name="contract_type" id="contract_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contract_type') border-red-500 @enderror">
                                @foreach($contractTypes as $type)
                                    <option value="{{ $type }}" {{ old('contract_type', $contract->contract_type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                            @error('contract_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror">
                            @error('start_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Salary & Payment</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Base Salary *</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary', $contract->salary) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('salary') border-red-500 @enderror">
                            @error('salary') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                            <select name="currency" id="currency" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-500 @enderror">
                                <option value="TZS" {{ old('currency', $contract->currency) == 'TZS' ? 'selected' : '' }}>TZS</option>
                                <option value="USD" {{ old('currency', $contract->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency', $contract->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                            @error('currency') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="payment_frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequency *</label>
                            <select name="payment_frequency" id="payment_frequency" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('payment_frequency') border-red-500 @enderror">
                                <option value="monthly" {{ old('payment_frequency', $contract->payment_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="bi-weekly" {{ old('payment_frequency', $contract->payment_frequency) == 'bi-weekly' ? 'selected' : '' }}>Bi-weekly</option>
                                <option value="weekly" {{ old('payment_frequency', $contract->payment_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                            @error('payment_frequency') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Job Description</h2>
                    <textarea name="job_description" id="job_description" rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('job_description') border-red-500 @enderror"
                        placeholder="Enter the detailed job description and responsibilities...">{{ old('job_description', $contract->job_description) }}</textarea>
                    @error('job_description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Contract Status *</label>
                            <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                @foreach($contractTypes as $type)
                                    <option value="{{ $type }}" {{ old('status', $contract->status) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="auto_renewal" id="auto_renewal" value="1" {{ old('auto_renewal', $contract->auto_renewal) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="auto_renewal" class="ml-2 block text-sm text-gray-700">Enable Auto Renewal</label>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                        Update Contract
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
