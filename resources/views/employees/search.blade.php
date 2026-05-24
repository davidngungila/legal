@extends('layouts.app')

@section('title', 'Search Results - Employees')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Search Results</h1>
            <p class="text-gray-600 mt-2">Found {{ $employees->total() }} results for "{{ $query }}"</p>
        </div>
        <div>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    @if($employees->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Employee</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Department</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Position</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-indigo-600 font-bold">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->department }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->position }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-{{ $employee->status_badge_color }}-100 text-{{ $employee->status_badge_color }}-700 rounded-full text-xs font-medium capitalize">
                                    {{ $employee->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <a href="{{ route('employees.show', $employee->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $employees->appends(['q' => $query])->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-feather="search" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">No results found</h2>
            <p class="text-gray-500 max-w-sm mx-auto">We couldn't find any employees matching "{{ $query }}". Try searching for a name, email, or employee ID.</p>
            <div class="mt-8">
                <a href="{{ route('employees.index') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all">
                    View All Employees
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
