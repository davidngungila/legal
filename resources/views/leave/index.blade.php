@extends('layouts.app')

@section('title', 'Leave Management - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Leave Management</h1>
            <p class="text-gray-600 mt-2">Manage employee leave requests and entitlements</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button type="button" onclick="document.getElementById('newLeaveRequestModal').classList.remove('hidden'); document.getElementById('newLeaveRequestModal').classList.add('flex'); document.body.style.overflow = 'hidden'; if(typeof feather !== 'undefined') feather.replace();" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Leave Request
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $leaveRequests->total() }}</h3>
            <p class="text-gray-600 text-sm">Total Requests</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $leaveRequests->where('status', 'pending')->count() }}</h3>
            <p class="text-gray-600 text-sm">Pending Requests</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $leaveRequests->where('status', 'approved')->count() }}</h3>
            <p class="text-gray-600 text-sm">Approved Requests</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $leaveTypes->count() }}</h3>
            <p class="text-gray-600 text-sm">Leave Types</p>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Leave Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($request->employee->first_name[0] ?? 'E', 0, 1) }}{{ substr($request->employee->last_name[0] ?? 'E', 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->employee->first_name }} {{ $request->employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->leave_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->days }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($request->status == 'approved') bg-green-100 text-green-800
                                @elseif($request->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($request->status == 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <!-- View -->
                                <button type="button" onclick="viewLeaveRequest({{ $request->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer p-1" title="View Details">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Edit -->
                                <button type="button" onclick="openUpdateModal({{ $request->id }})" class="text-indigo-600 hover:text-indigo-900 cursor-pointer p-1" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Approve (only for pending) -->
                                @if($request->status == 'pending')
                                <button type="button" onclick="approveLeaveRequest({{ $request->id }})" class="text-green-600 hover:text-green-900 cursor-pointer p-1" title="Approve">
                                    <i data-feather="check" class="w-4 h-4"></i>
                                </button>
                                
                                <!-- Reject (only for pending) -->
                                <button type="button" onclick="rejectLeaveRequest({{ $request->id }})" class="text-red-600 hover:text-red-900 cursor-pointer p-1" title="Reject">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                                @endif
                                
                                <!-- Delete -->
                                <button type="button" onclick="deleteLeaveRequest({{ $request->id }})" class="text-gray-600 hover:text-gray-900 cursor-pointer p-1" title="Delete">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">No leave requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $leaveRequests->links() }}
        </div>
    </div>
</div>

<!-- New Leave Request Modal -->
<div id="newLeaveRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">New Leave Request</h3>
                <button type="button" onclick="document.getElementById('newLeaveRequestModal').classList.add('hidden'); document.getElementById('newLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('leave.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                        @endforeach
                    </select>
                    @if($employees->isEmpty())
                    <p class="mt-1 text-sm text-red-600">No active employees found for this client.</p>
                    @endif
                </div>
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                    <select id="leave_type_id" name="leave_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                    @if($leaveTypes->isEmpty())
                    <p class="mt-1 text-sm text-red-600">No leave types found for this client.</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('newLeaveRequestModal').classList.add('hidden'); document.getElementById('newLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Leave Request Modal -->
<div id="updateLeaveRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Update Leave Request</h3>
                <button type="button" onclick="document.getElementById('updateLeaveRequestModal').classList.add('hidden'); document.getElementById('updateLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <form action="" method="POST" id="updateLeaveRequestForm" class="p-6">
            @csrf
            @method('PUT')
            <input type="hidden" id="update_request_id" name="request_id" value="">
            <div class="space-y-4">
                <div>
                    <label for="update_employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select id="update_employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="update_leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                    <select id="update_leave_type_id" name="leave_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="update_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="update_start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="update_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="update_end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div>
                    <label for="update_reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="update_reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label for="update_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="update_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('updateLeaveRequestModal').classList.add('hidden'); document.getElementById('updateLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Request</button>
            </div>
        </form>
    </div>
</div>

<!-- View Leave Request Modal -->
<div id="viewLeaveRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Leave Request Details</h3>
                <button type="button" onclick="document.getElementById('viewLeaveRequestModal').classList.add('hidden'); document.getElementById('viewLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium" id="view_employee_initials">--</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900" id="view_employee_name">--</p>
                        <p class="text-sm text-gray-500" id="view_employee_id">--</p>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Leave Type</p>
                            <p class="font-medium text-gray-900" id="view_leave_type">--</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span id="view_status_badge" class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">--</span>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Start Date</p>
                            <p class="font-medium text-gray-900" id="view_start_date">--</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End Date</p>
                            <p class="font-medium text-gray-900" id="view_end_date">--</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Days</p>
                            <p class="font-medium text-gray-900" id="view_days">--</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Applied At</p>
                            <p class="font-medium text-gray-900" id="view_applied_at">--</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500">Reason</p>
                    <p class="font-medium text-gray-900" id="view_reason">--</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="document.getElementById('viewLeaveRequestModal').classList.add('hidden'); document.getElementById('viewLeaveRequestModal').classList.remove('flex'); document.body.style.overflow = 'auto';" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewLeaveRequest(id) {
    console.log('Loading leave request:', id);
    fetch(`/leave/${id}`)
        .then(r=>r.json())
        .then(d=>{
            console.log('Response:', d);
            if(d.success){
                const r=d.request;
                document.getElementById('view_employee_name').textContent=`${r.employee.first_name} ${r.employee.last_name}`;
                document.getElementById('view_employee_id').textContent=r.employee.employee_id;
                document.getElementById('view_employee_initials').textContent=`${r.employee.first_name[0]}${r.employee.last_name[0]}`;
                document.getElementById('view_leave_type').textContent=r.leave_type;
                document.getElementById('view_start_date').textContent=r.start_date;
                document.getElementById('view_end_date').textContent=r.end_date;
                document.getElementById('view_days').textContent=r.days;
                document.getElementById('view_reason').textContent=r.reason||'No reason provided';
                document.getElementById('view_applied_at').textContent=r.applied_at||'N/A';
                const b=document.getElementById('view_status_badge');
                b.className='px-2 py-1 text-xs font-semibold rounded-full';
                if(r.status=='approved')b.classList.add('bg-green-100','text-green-800');
                else if(r.status=='pending')b.classList.add('bg-yellow-100','text-yellow-800');
                else if(r.status=='rejected')b.classList.add('bg-red-100','text-red-800');
                else b.classList.add('bg-gray-100','text-gray-800');
                b.textContent=r.status.charAt(0).toUpperCase()+r.status.slice(1);
                document.getElementById('viewLeaveRequestModal').classList.remove('hidden');
                document.getElementById('viewLeaveRequestModal').classList.add('flex');
                document.body.style.overflow='hidden';
                if(typeof feather!=='undefined')feather.replace();
            }else{
                console.error('Server error:', d.message);
                alert(d.message||'Failed to load leave request');
            }
        }).catch(e=>{
            console.error('Network error:', e);
            alert('Failed to load leave request: '+e.message);
        });
}

function openUpdateModal(id) {
    const m=document.getElementById('updateLeaveRequestModal');
    const f=document.getElementById('updateLeaveRequestForm');
    f.action=`/leave/${id}`;
    fetch(`/leave/${id}`)
        .then(r=>r.json())
        .then(d=>{
            if(d.success){
                document.getElementById('update_employee_id').value=d.request.employee_id;
                document.getElementById('update_leave_type_id').value=d.request.leave_type_id;
                document.getElementById('update_start_date').value=d.request.start_date;
                document.getElementById('update_end_date').value=d.request.end_date;
                document.getElementById('update_reason').value=d.request.reason||'';
                document.getElementById('update_status').value=d.request.status||'pending';
                document.getElementById('update_request_id').value=id;
                m.classList.remove('hidden');
                m.classList.add('flex');
                document.body.style.overflow='hidden';
                if(typeof feather!=='undefined')feather.replace();
            }
        }).catch(e=>{console.error(e);alert('Failed to load leave request');});
}

function approveLeaveRequest(id) {
    if(confirm('Are you sure you want to approve this leave request?')){
        fetch(`/leave/${id}/approve`,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type':'application/json',
                'Accept':'application/json'
            },
            body:JSON.stringify({status:'approved'})
        }).then(r=>r.json()).then(d=>{
            if(d.success){
                alert('Leave request approved successfully!');
                location.reload();
            }else{
                alert(d.message||'Failed to approve');
            }
        }).catch(e=>{console.error(e);alert('Failed to approve');});
    }
}

function rejectLeaveRequest(id) {
    const reason=prompt('Please provide a reason for rejection:');
    if(reason){
        fetch(`/leave/${id}/reject`,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type':'application/json',
                'Accept':'application/json'
            },
            body:JSON.stringify({status:'rejected',reason:reason})
        }).then(r=>r.json()).then(d=>{
            if(d.success){
                alert('Leave request rejected successfully!');
                location.reload();
            }else{
                alert(d.message||'Failed to reject');
            }
        }).catch(e=>{console.error(e);alert('Failed to reject');});
    }
}

function deleteLeaveRequest(id) {
    if(confirm('Are you sure you want to delete this leave request? This action cannot be undone.')){
        fetch(`/leave/${id}`,{
            method:'DELETE',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept':'application/json'
            }
        }).then(r=>r.json()).then(d=>{
            if(d.success){
                alert('Leave request deleted successfully!');
                location.reload();
            }else{
                alert(d.message||'Failed to delete');
            }
        }).catch(e=>{console.error(e);alert('Failed to delete');});
    }
}
</script>
@endsection
