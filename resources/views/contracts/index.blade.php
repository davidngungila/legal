@extends('layouts.app')

@section('title', 'Contract Management')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Contract Management</h1>
            <p class="text-gray-600 mt-2">Manage employee employment contracts and agreements</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('contracts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Create Contract
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Contracts</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Expiring Soon</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['expiring_soon'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Renewals Due</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['renewals_due'] }}</p>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Contract #</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Employee</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Type</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Start Date</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($contracts as $contract)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-indigo-600">
                            {{ $contract->formatted_contract_number }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ $contract->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $contract->employee->position }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \App\Models\Contract::CONTRACT_TYPES[$contract->contract_type] ?? str_replace('_', ' ', $contract->contract_type) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $contract->start_date->format('d M, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-{{ $contract->status_badge_color }}-100 text-{{ $contract->status_badge_color }}-700 rounded-full text-xs font-medium capitalize">
                                {{ $contract->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('contracts.show', $contract->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('contracts.edit', $contract->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
@endsection
