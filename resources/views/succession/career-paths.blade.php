@extends('layouts.app')

@section('title', 'Career Paths - Succession Planning - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Career Paths</h1>
            <p class="text-gray-600 mt-2">Visualize career progression and development opportunities</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createCareerPathModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Create Career Path
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="git-branch" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_paths']) }}</h3>
            <p class="text-gray-600 text-sm">Total Paths</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="layers" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_levels']) }}</h3>
            <p class="text-gray-600 text-sm">Total Levels</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_members']) }}</h3>
            <p class="text-gray-600 text-sm">Employees on Paths</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_paths']) }}</h3>
            <p class="text-gray-600 text-sm">Active Paths</p>
        </div>
    </div>

    <!-- Career Path Cards -->
    @forelse($paths as $path)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i data-feather="git-branch" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $path->name }}</h3>
                        @if($path->department)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">{{ $path->department }}</span>
                        @endif
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $path->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($path->status) }}</span>
                    </div>
                    @if($path->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $path->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-600">{{ $path->levels->count() }} levels • {{ $path->members->count() }} employees</span>
                <button onclick="openModal('editCareerPathModal{{ $path->id }}')" class="text-blue-600 hover:text-blue-900" title="Edit path">
                    <i data-feather="edit-2" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteCareerPath({{ $path->id }})" class="text-red-600 hover:text-red-900" title="Delete path">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Levels ladder -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-900">Career Ladder</h4>
                    <button onclick="openModal('addLevelModal{{ $path->id }}')" class="text-xs text-indigo-600 font-medium hover:underline inline-flex items-center gap-1">
                        <i data-feather="plus" class="w-3 h-3"></i>
                        Add Level
                    </button>
                </div>
                @if($path->levels->count())
                <div class="space-y-2">
                    @foreach($path->levels as $level)
                    <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-100">
                        <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-indigo-600 font-bold text-sm">{{ $level->level_order }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm">{{ $level->title }}</p>
                            <p class="text-xs text-gray-600">
                                @if($level->typical_time)Typical time: {{ $level->typical_time }}@endif
                                @if($level->competencies) • {{ \Illuminate\Support\Str::limit($level->competencies, 50) }}@endif
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <button onclick="openModal('editLevelModal{{ $level->id }}')" class="text-blue-600 hover:text-blue-900" title="Edit level">
                                <i data-feather="edit-2" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="deleteLevel({{ $level->id }})" class="text-red-500 hover:text-red-700" title="Delete level">
                                <i data-feather="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 bg-gray-50 rounded-lg px-4 py-3">No levels defined yet. Add the first level to build the career ladder.</p>
                @endif
            </div>

            <!-- Members -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-900">Employees on Path</h4>
                    <button onclick="openModal('addPathMemberModal{{ $path->id }}')" class="text-xs text-indigo-600 font-medium hover:underline inline-flex items-center gap-1">
                        <i data-feather="plus" class="w-3 h-3"></i>
                        Add Employee
                    </button>
                </div>
                @if($path->members->count())
                <div class="space-y-2">
                    @foreach($path->members as $member)
                    @php $currentLevel = $path->levels->firstWhere('level_order', $member->current_level_order); @endphp
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($member->employee->first_name ?? 'E', 0, 1)) }}{{ strtoupper(substr($member->employee->last_name ?? 'E', 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $member->employee->first_name }} {{ $member->employee->last_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $currentLevel?->title ?? 'Level ' . $member->current_level_order }}</p>
                        </div>
                        <form action="{{ route('succession.career-paths.members.update', $member->id) }}" method="POST" class="inline-block shrink-0">
                            @csrf
                            @method('PATCH')
                            <select name="current_level_order" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($path->levels as $level)
                                <option value="{{ $level->level_order }}" {{ $member->current_level_order == $level->level_order ? 'selected' : '' }}>Level {{ $level->level_order }}</option>
                                @endforeach
                            </select>
                        </form>
                        <button onclick="deletePathMember({{ $member->id }})" class="text-red-500 hover:text-red-700 shrink-0" title="Remove employee">
                            <i data-feather="user-x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 bg-gray-50 rounded-lg px-4 py-3">No employees on this path yet.</p>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i data-feather="git-branch" class="w-12 h-12 text-gray-400 mb-4 mx-auto"></i>
        <p class="text-lg font-medium text-gray-900">No career paths yet</p>
        <p class="text-sm text-gray-600 mt-2">Click "Create Career Path" to define your first career track</p>
    </div>
    @endforelse

    <!-- Create Career Path Modal -->
    <x-advanced-modal id="createCareerPathModal" title="Create Career Path" description="Define a career track with progression levels." icon="plus" color="indigo" size="2xl">
        <form id="createCareerPathForm" method="POST" action="{{ route('succession.career-paths.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Path Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. HR Career Track">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" name="department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Human Resources">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Purpose of this career track..."></textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('createCareerPathModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="createCareerPathForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Path</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    @foreach($paths as $path)
    <!-- Edit Career Path Modal -->
    <x-advanced-modal id="editCareerPathModal{{ $path->id }}" title="Edit Career Path" description="Update the career path details." icon="edit" color="indigo" size="2xl">
        <form id="editCareerPathForm{{ $path->id }}" method="POST" action="{{ route('succession.career-paths.update', $path->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Path Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ $path->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" name="department" value="{{ $path->department }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" {{ $path->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $path->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $path->description }}</textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('editCareerPathModal{{ $path->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="editCareerPathForm{{ $path->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    <!-- Add Level Modal -->
    <x-advanced-modal id="addLevelModal{{ $path->id }}" title="Add Level to {{ $path->name }}" description="Add the next level in this career path." icon="plus" color="indigo" size="2xl">
        <form id="addLevelForm{{ $path->id }}" method="POST" action="{{ route('succession.career-paths.levels.store', $path->id) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Senior HR Manager">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typical Time</label>
                    <input type="text" name="typical_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 4-5 years">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Key Competencies</label>
                    <textarea name="competencies" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Skills required at this level..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
                    <textarea name="responsibilities" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Key responsibilities..."></textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addLevelModal{{ $path->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="addLevelForm{{ $path->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Level</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    <!-- Add Path Member Modal -->
    <x-advanced-modal id="addPathMemberModal{{ $path->id }}" title="Add Employee to {{ $path->name }}" description="Assign an employee to this career path." icon="plus" color="indigo" size="2xl">
        @php $existingEmployeeIds = $path->members->pluck('employee_id')->all(); @endphp
        <form id="addPathMemberForm{{ $path->id }}" method="POST" action="{{ route('succession.career-paths.members.store', $path->id) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                        @if(!in_array($employee->id, $existingEmployeeIds))
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})@if($employee->position) - {{ $employee->position }}@endif</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Level</label>
                    <select name="current_level_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @forelse($path->levels as $level)
                        <option value="{{ $level->level_order }}">Level {{ $level->level_order }} - {{ $level->title }}</option>
                        @empty
                        <option value="1">Level 1</option>
                        @endforelse
                    </select>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addPathMemberModal{{ $path->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="addPathMemberForm{{ $path->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Employee</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    <!-- Edit Level Modals -->
    @foreach($path->levels as $level)
    <x-advanced-modal id="editLevelModal{{ $level->id }}" title="Edit Level" description="Update the career level details." icon="edit" color="indigo" size="2xl">
        <form id="editLevelForm{{ $level->id }}" method="POST" action="{{ route('succession.career-paths.levels.update', $level->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ $level->title }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typical Time</label>
                    <input type="text" name="typical_time" value="{{ $level->typical_time }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Key Competencies</label>
                    <textarea name="competencies" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $level->competencies }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsibilities</label>
                    <textarea name="responsibilities" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $level->responsibilities }}</textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('editLevelModal{{ $level->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="editLevelForm{{ $level->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>
    @endforeach
    @endforeach
</div>

@push('scripts')
<script>
function deleteCareerPath(id) {
    if (confirm('Are you sure you want to delete this career path and all its levels and members? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/career-paths/${id}`;

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

function deleteLevel(id) {
    if (confirm('Delete this career level?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/levels/${id}`;

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

function deletePathMember(id) {
    if (confirm('Remove this employee from the career path?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/path-members/${id}`;

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
