@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <h1 class="h2">Departments</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
            <i data-feather="plus" class="me-2"></i>Add Department
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Parent Department</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr>
                            <td class="fw-semibold">{{ $department->name }}</td>
                            <td>{{ $department->code ?? '-' }}</td>
                            <td>{{ $department->parent->name ?? '-' }}</td>
                            <td>{{ $department->manager ? ($department->manager->first_name . ' ' . $department->manager->last_name) : '-' }}</td>
                            <td>
                                <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}">
                                        <i data-feather="edit" class="feather-sm"></i>
                                    </button>
                                    <form method="POST" action="{{ route('departments.destroy', $department->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i data-feather="trash-2" class="feather-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($departments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i data-feather="users" class="feather-lg mb-3"></i>
                    <p>No departments found. Create your first department!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Department Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('departments.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Code</label>
                                <input type="text" name="code" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Parent Department</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActiveDept" checked>
                                    <label class="form-check-label" for="isActiveDept">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Department</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Department Modals -->
    @foreach($departments as $department)
    <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('departments.update', $department->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Code</label>
                                <input type="text" name="code" class="form-control" value="{{ $department->code }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Parent Department</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($departments as $dept)
                                        @if($dept->id != $department->id)
                                            <option value="{{ $dept->id }}" {{ $department->parent_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">None</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $department->manager_id == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $department->description }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActiveDept{{ $department->id }}" {{ $department->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActiveDept{{ $department->id }}">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Department</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
