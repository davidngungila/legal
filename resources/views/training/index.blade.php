@extends('layouts.app')

@section('title', 'Training & Development - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training & Development</h1>
            <p class="text-gray-600 mt-2">Manage training programs, sessions and completions</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('newProgramModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Training Program
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="book-open" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active_programs'] }}</h3>
            <p class="text-gray-600 text-sm">Active Programs</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['participation_rate'] }}%</h3>
            <p class="text-gray-600 text-sm">Participation Rate</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['training_hours'] }}</h3>
            <p class="text-gray-600 text-sm">Training Hours</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</h3>
            <p class="text-gray-600 text-sm">Completion Rate</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('training.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input rounded-md border-gray-300" placeholder="Name, code, provider...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="form-select rounded-md border-gray-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select rounded-md border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('training.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Training Programs</h3>
        </div>
        @if($programs->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($programs as $program)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ $program->category ?: 'General' }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $program->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($program->status) }}</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-1">{{ $program->name }}</h4>
                @if($program->code)
                <p class="text-xs text-gray-500 mb-2">Code: {{ $program->code }}</p>
                @endif
                @if($program->is_certification)
                <span class="inline-flex items-center px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full mb-2">
                    <i data-feather="award" class="w-3 h-3 mr-1"></i> Certification
                </span>
                @endif
                <div class="space-y-2 my-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sessions:</span>
                        <span class="font-medium">{{ $program->sessions_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Enrollments:</span>
                        <span class="font-medium">{{ $program->enrollments_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">{{ $program->duration_hours }} hrs</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Provider:</span>
                        <span class="font-medium">{{ $program->provider ?: '—' }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <a href="{{ route('training.programs.show', $program->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage →</a>
                    <div class="flex space-x-3">
                        <button onclick='editProgram(@json($program))' class="text-gray-600 hover:text-gray-800 text-sm">Edit</button>
                        <form action="{{ route('training.programs.destroy', $program->id) }}" method="POST" onsubmit="return confirm('Delete this program and all its sessions?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $programs->links() }}
        </div>
        @else
        <div class="text-center text-gray-500 py-12">No training programs yet. Create your first program.</div>
        @endif
    </div>
</div>

<x-advanced-modal id="newProgramModal" title="New Training Program" icon="book-open" color="indigo" size="lg">
    <form action="{{ route('training.programs.store') }}" method="POST" id="newProgramForm">
        @csrf
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program Name</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Code</label>
                    <input type="text" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. LEAD-01">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="Management">Management</option>
                        <option value="Technical">Technical</option>
                        <option value="Soft Skills">Soft Skills</option>
                        <option value="Finance">Finance</option>
                        <option value="Compliance">Compliance</option>
                        <option value="Professional">Professional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Provider</label>
                    <input type="text" name="provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Internal / external provider">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cost</label>
                    <input type="number" name="cost" min="0" step="0.01" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                        <option value="KES">KES</option>
                        <option value="UGX">UGX</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (hours)</label>
                    <input type="number" name="duration_hours" min="0" step="0.5" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_certification" id="new_is_certification" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="new_is_certification" class="text-sm text-gray-700">Issues a certification on completion</label>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newProgramModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newProgramForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Program</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editProgramModal" title="Edit Training Program" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editProgramForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program Name</label>
                    <input type="text" id="edit_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Code</label>
                    <input type="text" id="edit_code" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" id="edit_category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Provider</label>
                    <input type="text" id="edit_provider" name="provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="edit_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cost</label>
                    <input type="number" id="edit_cost" name="cost" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select id="edit_currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                        <option value="KES">KES</option>
                        <option value="UGX">UGX</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (hours)</label>
                    <input type="number" id="edit_duration_hours" name="duration_hours" min="0" step="0.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_certification" id="edit_is_certification" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="edit_is_certification" class="text-sm text-gray-700">Issues a certification on completion</label>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editProgramModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editProgramForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Program</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editProgram(program) {
    document.getElementById('edit_name').value = program.name;
    document.getElementById('edit_code').value = program.code || '';
    document.getElementById('edit_category').value = program.category || '';
    document.getElementById('edit_provider').value = program.provider || '';
    document.getElementById('edit_description').value = program.description || '';
    document.getElementById('edit_cost').value = program.cost;
    document.getElementById('edit_currency').value = program.currency || 'TZS';
    document.getElementById('edit_duration_hours').value = program.duration_hours;
    document.getElementById('edit_status').value = program.status || 'active';
    document.getElementById('edit_is_certification').checked = program.is_certification == 1 || program.is_certification === true;
    document.getElementById('editProgramForm').action = '{{ route('training.programs.update', 0) }}'.replace(/\/0$/, '/' + program.id);
    openModal('editProgramModal');
}
</script>
@endsection
