@extends('layouts.app')

@section('title', 'Position Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Position Management</h1>
            <p class="text-gray-600 mt-2">Manage your company's job positions and roles efficiently</p>
            @if(session('current_client_id'))
                @php
                    $currentClient = \App\Models\Client::find(session('current_client_id'));
                @endphp
                @if($currentClient)
                <div class="mt-2 flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Managing positions for:</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
                </div>
                @endif
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" id="exportBtn" onclick="exportPositions()">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors inline-flex items-center" onclick="showModal('importPositionsModal')">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Bulk Import
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center" onclick="showModal('createPositionModal')">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Position
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Positions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Inactive</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-gray-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">With Salary Range</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['with_salary'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="positionSearch" placeholder="Search positions..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="departmentFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Positions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="positionsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($positions as $position)
                    <tr class="position-row" data-status="{{ $position->is_active ? 'active' : 'inactive' }}" data-department="{{ $position->department_id }}" data-title="{{ strtolower($position->title) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $position->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $position->description ? Str::limit($position->description, 30) : 'No description' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $position->department->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $position->job_code ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $position->grade_level ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($position->min_salary && $position->max_salary)
                                    {{ number_format($position->min_salary, 2) }} - {{ number_format($position->max_salary, 2) }}
                                @elseif($position->min_salary)
                                    {{ number_format($position->min_salary, 2) }}+
                                @elseif($position->max_salary)
                                    Up to {{ number_format($position->max_salary, 2) }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $position->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $position->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" onclick="showModal('editPositionModal{{ $position->id }}')">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button class="{{ $position->is_active ? 'text-green-600 hover:text-green-900' : 'text-gray-400 hover:text-gray-600' }}" onclick="togglePositionStatus({{ $position->id }}, {{ $position->is_active ? 'false' : 'true' }})" title="{{ $position->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i data-feather="{{ $position->is_active ? 'check-circle' : 'circle' }}" class="w-4 h-4"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" onclick="deletePosition({{ $position->id }})">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="briefcase" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No positions found</p>
                                <p class="text-sm text-gray-600 mt-2">Get started by creating your first position</p>
                                <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" onclick="showModal('createPositionModal')">
                                    <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                                    Add Position
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Positions Modal -->
<div id="importPositionsModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Bulk Import Positions</h3>
                <button type="button" onclick="hideModal('importPositionsModal')" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-blue-800 mb-2">Import Instructions</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Upload a CSV file with position data</li>
                        <li>• Required columns: title, department_id</li>
                        <li>• Optional columns: job_code, grade_level, min_salary, max_salary, description, requirements, is_active</li>
                        <li>• First row should contain column headers</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <a href="/positions/import-template" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        <i data-feather="download" class="w-4 h-4 inline mr-1"></i>
                        Download CSV Template
                    </a>
                </div>
            </div>
            
            <form id="importPositionsForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">CSV File <span class="text-red-500">*</span></label>
                    <input type="file" name="csv_file" accept=".csv" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div id="importResults" class="hidden mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Import Results</h4>
                        <div id="importResultsContent"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
            <button type="button" onclick="hideModal('importPositionsModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="button" onclick="importPositions()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Import</button>
        </div>
    </div>
</div>

<!-- Create Position Modal -->
<div id="createPositionModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form method="POST" action="{{ route('positions.store') }}">
            @csrf
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Add New Position</h3>
                    <button type="button" onclick="hideModal('createPositionModal')" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Software Engineer">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Code</label>
                        <input type="text" name="job_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., SE-001">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <input type="number" name="grade_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="1" max="20">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary</label>
                        <input type="number" name="min_salary" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" min="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary</label>
                        <input type="number" name="max_salary" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" min="0">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Brief job description..."></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea name="requirements" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Job requirements..."></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="isActive" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="isActive" class="ml-2 block text-sm text-gray-700">
                                Position is active
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="hideModal('createPositionModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Position</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Position Modals -->
@foreach($positions as $position)
<div id="editPositionModal{{ $position->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form method="POST" action="{{ route('positions.update', $position->id) }}">
            @csrf
            @method('PUT')
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Edit Position</h3>
                    <button type="button" onclick="hideModal('editPositionModal{{ $position->id }}')" class="text-gray-400 hover:text-gray-600">
                        <i data-feather="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $position->title }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ $position->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Code</label>
                        <input type="text" name="job_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $position->job_code }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <input type="number" name="grade_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="1" max="20" value="{{ $position->grade_level }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary</label>
                        <input type="number" name="min_salary" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" min="0" value="{{ $position->min_salary }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary</label>
                        <input type="number" name="max_salary" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" min="0" value="{{ $position->max_salary }}">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $position->description }}</textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                        <textarea name="requirements" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $position->requirements }}</textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="isActive{{ $position->id }}" value="1" {{ $position->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="isActive{{ $position->id }}" class="ml-2 block text-sm text-gray-700">
                                Position is active
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" onclick="hideModal('editPositionModal{{ $position->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Position</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter functionality
    function filterPositions() {
        const search = document.getElementById('positionSearch').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const department = document.getElementById('departmentFilter').value;
        
        document.querySelectorAll('.position-row').forEach(row => {
            const titleMatch = row.dataset.title.includes(search);
            const statusMatch = !status || row.dataset.status === status;
            const deptMatch = !department || row.dataset.department === department;
            
            if (titleMatch && statusMatch && deptMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Event listeners
    document.getElementById('positionSearch').addEventListener('input', filterPositions);
    document.getElementById('statusFilter').addEventListener('change', filterPositions);
    document.getElementById('departmentFilter').addEventListener('change', filterPositions);
    
    // Close modals when clicking outside
    document.querySelectorAll('[id^="createPositionModal"], [id^="editPositionModal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal(modal.id);
            }
        });
    });
});

function exportPositions() {
    window.open('/positions/export', '_blank');
    showNotification('Positions export initiated!', 'info');
}

function importPositions() {
    const form = document.getElementById('importPositionsForm');
    const fileInput = form.querySelector('input[type="file"]');
    
    if (!fileInput.files.length) {
        showNotification('Please select a CSV file to import', 'error');
        return;
    }
    
    const formData = new FormData(form);
    const resultsDiv = document.getElementById('importResults');
    const resultsContent = document.getElementById('importResultsContent');
    
    resultsDiv.classList.remove('hidden');
    resultsContent.innerHTML = '<p class="text-gray-600">Importing positions...</p>';
    
    fetch('/positions/import', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultsContent.innerHTML = `
                <div class="text-green-600 font-medium mb-2">Import completed successfully!</div>
                <div class="text-sm text-gray-700">
                    <p>Imported: ${data.imported} positions</p>
                    ${data.skipped > 0 ? `<p class="text-yellow-600">Skipped: ${data.skipped} positions (duplicates)</p>` : ''}
                    ${data.errors ? `<p class="text-red-600 mt-2">Errors: ${data.errors}</p>` : ''}
                </div>
            `;
            showNotification('Positions imported successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            resultsContent.innerHTML = `
                <div class="text-red-600 font-medium mb-2">Import failed</div>
                <div class="text-sm text-gray-700">
                    <p>${data.message || 'An error occurred during import'}</p>
                    ${data.errors ? `<p class="mt-2">${data.errors}</p>` : ''}
                </div>
            `;
            showNotification('Import failed', 'error');
        }
    })
    .catch(error => {
        resultsContent.innerHTML = `
            <div class="text-red-600 font-medium mb-2">Import failed</div>
            <div class="text-sm text-gray-700">
                <p>An error occurred during import: ${error.message}</p>
            </div>
        `;
        showNotification('Import failed', 'error');
    });
}

function togglePositionStatus(id, status) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/positions/${id}/toggle-status`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'is_active';
    statusInput.value = status;
    
    form.appendChild(csrfToken);
    form.appendChild(statusInput);
    document.body.appendChild(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ is_active: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            window.location.reload();
        } else {
            showNotification(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        showNotification('Failed to update status', 'error');
    })
    .finally(() => {
        document.body.removeChild(form);
    });
}

function deletePosition(id) {
    if (confirm('Are you sure you want to delete this position? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/positions/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
