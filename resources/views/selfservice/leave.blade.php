@extends('layouts.app')

@section('title', 'Apply for Leave - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Apply for Leave</h1>
            <p class="text-gray-600 mt-2">Request annual, sick, or emergency leave</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Leave Balance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="sun" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">18</p>
            <p class="text-sm text-gray-500">Annual Leave Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="heart" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">75</p>
            <p class="text-sm text-gray-500">Sick Leave Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="home" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">84</p>
            <p class="text-sm text-gray-500">Maternity/Paternity Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">7</p>
            <p class="text-sm text-gray-500">Compassionate Days</p>
        </div>
    </div>

    <!-- Leave Application Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Leave Application Details</h2>
        
        <form method="POST" action="{{ route('selfservice.leave.store') }}" class="space-y-6">
            @csrf
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Leave Title *</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="e.g., Annual Leave Request">
                </div>
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                    <input type="date" id="end_date" name="end_date" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-2">Leave Type *</label>
                    <select id="leave_type" name="leave_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Leave Type</option>
                        <option value="annual">Annual Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="emergency">Emergency Leave</option>
                        <option value="maternity">Maternity Leave</option>
                        <option value="paternity">Paternity Leave</option>
                        <option value="compassionate">Compassionate Leave</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Reason for Leave *</label>
                <textarea id="description" name="description" rows="4" required 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Please provide detailed reason for your leave request..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('selfservice') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Leave Request
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Leave Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Leave Requests</h2>
            <a href="{{ route('selfservice') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All History</a>
        </div>
        
        <div class="space-y-4">
            @if($employee && $employee->selfServiceRequests->where('request_type', 'leave')->count() > 0)
                @foreach($employee->selfServiceRequests->where('request_type', 'leave')->take(3) as $request)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        {!! $request->status_badge !!}
                        <span class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">{{ $request->title }}</p>
                    @if($request->start_date && $request->end_date)
                        <p class="text-sm text-gray-600 mb-2">{{ $request->start_date }} - {{ $request->end_date }} ({{ $request->days_requested ?? 'N/A' }} days)</p>
                    @endif
                    <p class="text-xs text-gray-500">
                        @if($request->status === 'approved')
                            Approved by: {{ $request->approver->name ?? 'HR Manager' }}
                        @elseif($request->status === 'pending')
                            Awaiting approval from: Department Manager
                        @elseif($request->status === 'rejected')
                            Rejected: {{ $request->rejection_reason ?? 'Not specified' }}
                        @endif
                    </p>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="calendar" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                    <p>No leave requests found.</p>
                    <p class="text-sm mt-2">Submit your first leave request to see it here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
