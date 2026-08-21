@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Audit Trail</h1>
            <p class="text-gray-600 mt-2">Track all system activities and changes</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="exportCsv()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i data-feather="download" class="w-4 h-4 mr-2"></i>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                <select name="event" id="filterEvent" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                    <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select name="user_id" id="filterUser" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                <select name="module" id="filterModule" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                    <option value="{{ $module }}">{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" id="filterDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" id="filterDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </form>
        <div class="mt-4 flex justify-end space-x-3">
            <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </button>
            <button onclick="applyFilters()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Apply Filters
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="auditTableBody">
                    @foreach($audits as $audit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($audit->user && $audit->user->profile_photo_url)
                                    <img src="{{ $audit->user->profile_photo_url }}" alt="Profile" class="w-8 h-8 rounded-full object-cover mr-3">
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white text-xs font-bold">{{ $audit->user ? substr($audit->user->first_name, 0, 1) : 'S' }}</span>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $audit->user ? $audit->user->first_name . ' ' . $audit->user->last_name : 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($audit->event == 'created')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Create</span>
                            @elseif($audit->event == 'updated')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Update</span>
                            @elseif($audit->event == 'deleted')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Delete</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">{{ ucfirst($audit->event) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $audit->module ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $audit->description ?: 'No description' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $audit->ip_address ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewAuditDetails({{ $audit->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">Showing {{ $audits->firstItem() }}-{{ $audits->lastItem() }} of {{ $audits->total() }} results</p>
        <div class="flex space-x-2">
            {{ $audits->links() }}
        </div>
    </div>
</div>

<!-- Audit Details Modal -->
<x-advanced-modal id="auditDetailsModal" title="Audit Details" icon="clipboard" color="purple" size="3xl">
    <div id="auditDetailsContent">
        <!-- Content will be loaded here -->
    </div>
</x-advanced-modal>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Handle filter changes from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('event')) {
            document.getElementById('filterEvent').value = urlParams.get('event');
        }
        if (urlParams.get('user_id')) {
            document.getElementById('filterUser').value = urlParams.get('user_id');
        }
        if (urlParams.get('module')) {
            document.getElementById('filterModule').value = urlParams.get('module');
        }
        if (urlParams.get('date_from')) {
            document.getElementById('filterDateFrom').value = urlParams.get('date_from');
        }
        if (urlParams.get('date_to')) {
            document.getElementById('filterDateTo').value = urlParams.get('date_to');
        }
    });
    
    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        window.location.search = params.toString();
    }
    
    function resetFilters() {
        window.location.href = '{{ route('audit-trail.index') }}';
    }
    
    function exportCsv() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        window.location.href = '{{ route('audit-trail.export') }}?' + params.toString();
    }
    
    async function viewAuditDetails(auditId) {
        try {
            const response = await fetch(`/audit-trail/${auditId}`, {
                headers: {
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();
            
            if (data.success) {
                const audit = data.audit;
                let oldValuesHtml = '';
                let newValuesHtml = '';
                
                if (audit.old_values) {
                    oldValuesHtml = '<div class="bg-gray-50 p-4 rounded-lg mb-4"><h4 class="font-semibold text-gray-800 mb-2">Old Values</h4><pre class="text-xs text-gray-700 overflow-x-auto">' + JSON.stringify(audit.old_values, null, 2) + '</pre></div>';
                }
                
                if (audit.new_values) {
                    newValuesHtml = '<div class="bg-blue-50 p-4 rounded-lg"><h4 class="font-semibold text-gray-800 mb-2">New Values</h4><pre class="text-xs text-gray-700 overflow-x-auto">' + JSON.stringify(audit.new_values, null, 2) + '</pre></div>';
                }
                
                let userHtml = '';
                if (audit.user) {
                    const userPhoto = audit.user.profile_photo_url 
                        ? `<img src="${audit.user.profile_photo_url}" alt="Profile" class="w-12 h-12 rounded-full object-cover mr-3">`
                        : `<div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white text-xl font-bold">${audit.user.first_name.charAt(0)}${audit.user.last_name.charAt(0)}</span>
                           </div>`;
                    userHtml = `<div class="flex items-center">${userPhoto}<span>${audit.user.first_name} ${audit.user.last_name}</span></div>`;
                } else {
                    userHtml = '<span>System</span>';
                }

                document.getElementById('auditDetailsContent').innerHTML = `
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <span class="text-sm text-gray-500">Timestamp</span>
                            <p class="font-medium">${audit.created_at}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">User</span>
                            <p class="font-medium">${userHtml}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Event</span>
                            <p class="font-medium capitalize">${audit.event}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Module</span>
                            <p class="font-medium">${audit.module || '-'}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">IP Address</span>
                            <p class="font-medium">${audit.ip_address || '-'}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Client</span>
                            <p class="font-medium">${audit.client ? audit.client.name : '-'}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-sm text-gray-500">User Agent</span>
                            <p class="font-medium text-sm break-all">${audit.user_agent || '-'}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-sm text-gray-500">URL</span>
                            <p class="font-medium text-sm break-all">${audit.url || '-'}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-sm text-gray-500">Description</span>
                            <p class="font-medium">${audit.description || '-'}</p>
                        </div>
                    </div>
                    ${oldValuesHtml}
                    ${newValuesHtml}
                `;
                
                openModal('auditDetailsModal');
            }
        } catch (error) {
            console.error('Error fetching audit details:', error);
        }
    }
    
    function closeAuditDetails() {
        closeModal('auditDetailsModal');
    }
</script>
@endsection
