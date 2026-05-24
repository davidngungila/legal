@extends('layouts.app')

@section('title', 'Employee Details - ' . $employee->full_name)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Details</h1>
            <p class="text-gray-600 mt-2">Viewing detailed information for {{ $employee->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>
                Edit Employee
            </a>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-32 h-32 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-indigo-600">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                <p class="text-gray-500">{{ $employee->position }}</p>
                <div class="mt-4">
                    <span class="px-3 py-1 bg-{{ $employee->status_badge_color }}-100 text-{{ $employee->status_badge_color }}-700 rounded-full text-sm font-medium capitalize">
                        {{ $employee->status }}
                    </span>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-100 text-left space-y-4">
                    <div class="flex items-center text-gray-600">
                        <i data-feather="mail" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->email }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="phone" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->phone }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="map-pin" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->city }}, {{ $employee->country }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Tabs -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px px-6">
                        <button class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8">
                            Employment Info
                        </button>
                    </nav>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->employee_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Department</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->department }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Hire Date</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->hire_date ? $employee->hire_date->format('d M, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Employment Type</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Salary</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->formatted_salary }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Employee Documents</h3>
                @if($employee->documents && $employee->documents->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-3 text-sm font-semibold text-gray-600">Document Name</th>
                                    <th class="pb-3 text-sm font-semibold text-gray-600">Type</th>
                                    <th class="pb-3 text-sm font-semibold text-gray-600">Status</th>
                                    <th class="pb-3 text-sm font-semibold text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($employee->documents as $doc)
                                    <tr>
                                        <td class="py-3 text-sm text-gray-900">{{ $doc->document_name }}</td>
                                        <td class="py-3 text-sm text-gray-500">{{ $doc->document_type }}</td>
                                        <td class="py-3">
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">{{ $doc->status }}</span>
                                        </td>
                                        <td class="py-3">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <i data-feather="file" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                        <p class="text-gray-500 text-sm">No documents uploaded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
