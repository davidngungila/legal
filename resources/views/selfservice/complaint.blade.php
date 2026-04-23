@extends('layouts.app')

@section('title', 'File Complaint - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">File Complaint</h1>
            <p class="text-gray-600 mt-2">Submit grievances or concerns</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 mb-8">
        <div class="flex items-start space-x-3">
            <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600 mt-1"></i>
            <div>
                <h2 class="text-lg font-semibold text-orange-900 mb-2">Important Notice</h2>
                <p class="text-orange-800 text-sm">
                    All complaints are handled confidentially and in accordance with Tanzania Labour Act. 
                    Your submission will be legally tracked and processed according to company policies and legal requirements. 
                    False or malicious complaints may result in disciplinary action.
                </p>
            </div>
        </div>
    </div>

    <!-- Complaint Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Pending</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">2</p>
            <p class="text-sm text-gray-500">Under Review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Resolved</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">5</p>
            <p class="text-sm text-gray-500">Successfully Resolved</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="activity" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">In Progress</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">1</p>
            <p class="text-sm text-gray-500">Being Investigated</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="bar-chart" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">8</p>
            <p class="text-sm text-gray-500">All Complaints</p>
        </div>
    </div>

    <!-- Complaint Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Complaint Details</h2>
        
        <form method="POST" action="{{ route('selfservice.complaint.store') }}" class="space-y-6">
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
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Brief description of the issue...">
                </div>
                
                <div>
                    <label for="complaint_type" class="block text-sm font-medium text-gray-700 mb-2">Complaint Type *</label>
                    <select id="complaint_type" name="complaint_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Complaint Type</option>
                        <option value="workplace">Workplace Issue</option>
                        <option value="harassment">Harassment</option>
                        <option value="discrimination">Discrimination</option>
                        <option value="safety">Safety Concern</option>
                        <option value="policy">Policy Violation</option>
                        <option value="salary">Salary Issue</option>
                        <option value="working">Working Conditions</option>
                        <option value="management">Management Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                <textarea id="description" name="description" rows="6" required 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Please provide detailed description of your complaint, including specific dates, times, locations, and any witnesses..."></textarea>
            </div>
            
            <div>
                <label for="desired_resolution" class="block text-sm font-medium text-gray-700 mb-2">Desired Resolution *</label>
                <textarea id="desired_resolution" name="desired_resolution" rows="3" required 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="What would you like to see as the outcome of this complaint?"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="incident_date" class="block text-sm font-medium text-gray-700 mb-2">Date of Incident</label>
                    <input type="date" id="incident_date" name="incident_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="incident_time" class="block text-sm font-medium text-gray-700 mb-2">Time of Incident</label>
                    <input type="time" id="incident_time" name="incident_time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <label for="incident_location" class="block text-sm font-medium text-gray-700 mb-2">Location of Incident</label>
                <input type="text" id="incident_location" name="incident_location" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Where did the incident occur?">
            </div>
            
            <div>
                <label for="witnesses" class="block text-sm font-medium text-gray-700 mb-2">Witnesses (if any)</label>
                <textarea id="witnesses" name="witnesses" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Names and contact information of any witnesses..."></textarea>
            </div>
            
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="acknowledgement" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="acknowledgement" class="text-sm text-gray-700">
                    I confirm that the information provided is accurate and true to the best of my knowledge. I understand that false statements may result in disciplinary action.
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('selfservice') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Complaints -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Complaints</h2>
            <a href="{{ route('selfservice') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All History</a>
        </div>
        
        <div class="space-y-4">
            @if($employee && $employee->selfServiceRequests->where('request_type', 'complaint')->count() > 0)
                @foreach($employee->selfServiceRequests->where('request_type', 'complaint')->take(3) as $request)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        {!! $request->status_badge !!}
                        <span class="text-xs text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">{{ $request->title }}</p>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($request->description, 100) }}</p>
                    <p class="text-xs text-gray-500">
                        Reference: COMP-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }} 
                        @if($request->status === 'resolved')
                            | Resolved: {{ $request->approval_notes ?? 'Successfully resolved' }}
                        @elseif($request->status === 'pending')
                            | Status: Awaiting HR response
                        @elseif($request->status === 'in_progress')
                            | Status: Being investigated
                        @endif
                    </p>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="alert-circle" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                    <p>No complaints found.</p>
                    <p class="text-sm mt-2">Submit your first complaint to see it here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
