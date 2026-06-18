@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <h1 class="h2">Positions</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPositionModal">
            <i data-feather="plus" class="me-2"></i>Add Position
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
                            <th>Title</th>
                            <th>Department</th>
                            <th>Job Code</th>
                            <th>Grade Level</th>
                            <th>Salary Range</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                        <tr>
                            <td class="fw-semibold">{{ $position->title }}</td>
                            <td>{{ $position->department->name ?? '-' }}</td>
                            <td>{{ $position->job_code ?? '-' }}</td>
                            <td>{{ $position->grade_level ?? '-' }}</td>
                            <td>
                                @if($position->min_salary && $position->max_salary)
                                    {{ number_format($position->min_salary, 2) }} - {{ number_format($position->max_salary, 2) }}
                                @elseif($position->min_salary)
                                    {{ number_format($position->min_salary, 2) }}+
                                @elseif($position->max_salary)
                                    Up to {{ number_format($position->max_salary, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $position->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $position->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPositionModal{{ $position->id }}">
                                        <i data-feather="edit" class="feather-sm"></i>
                                    </button>
                                    <form method="POST" action="{{ route('positions.destroy', $position->id) }}" class="d-inline">
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
                @if($positions->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i data-feather="briefcase" class="feather-lg mb-3"></i>
                    <p>No positions found. Create your first position!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Position Modal -->
    <div class="modal fade" id="createPositionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('positions.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Position Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Code</label>
                                <input type="text" name="job_code" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade Level</label>
                                <input type="number" name="grade_level" class="form-control" min="1" max="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Salary</label>
                                <input type="number" name="min_salary" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Salary</label>
                                <input type="number" name="max_salary" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea name="requirements" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Position</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Position Modals -->
    @foreach($positions as $position)
    <div class="modal fade" id="editPositionModal{{ $position->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('positions.update', $position->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Position Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $position->title }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ $position->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Code</label>
                                <input type="text" name="job_code" class="form-control" value="{{ $position->job_code }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grade Level</label>
                                <input type="number" name="grade_level" class="form-control" min="1" max="20" value="{{ $position->grade_level }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Salary</label>
                                <input type="number" name="min_salary" class="form-control" step="0.01" min="0" value="{{ $position->min_salary }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Salary</label>
                                <input type="number" name="max_salary" class="form-control" step="0.01" min="0" value="{{ $position->max_salary }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $position->description }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea name="requirements" class="form-control" rows="3">{{ $position->requirements }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive{{ $position->id }}" {{ $position->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive{{ $position->id }}">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Position</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
