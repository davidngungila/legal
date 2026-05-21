@extends('layouts.app')

@section('title', 'Client Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Client Management</h1>
            <p class="text-gray-600 mt-2">Manage registered clients and their information</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('client-registration.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Register New Client
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search clients..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="regionFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Regions</option>
                    @foreach($clients->pluck('region')->unique() as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registrations
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="clientsTableBody">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors client-row" 
                            data-name="{{ $client->employer_name }}"
                            data-email="{{ $client->email }}"
                            data-region="{{ $client->region }}"
                            data-status="{{ $client->is_active ? 'active' : 'inactive' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">
                                                {{ strtoupper(substr($client->employer_name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->employer_name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $client->employer_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->contact_person }}</div>
                                <div class="text-sm text-gray-500">{{ $client->contact_email }}</div>
                                <div class="text-sm text-gray-500">{{ $client->contact_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $client->region }}</div>
                                <div class="text-sm text-gray-500">{{ $client->district }}</div>
                                <div class="text-sm text-gray-500">{{ $client->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs space-y-1">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                        <span>TIN: {{ $client->tin_number }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                                        <span>NSSF: {{ $client->nssf_registration }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                        <span>WCF: {{ $client->wcf_registration }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('client-registration.edit', $client) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @if($client->is_active)
                                        <button onclick="toggleClientStatus({{ $client->id }}, false)" 
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Deactivate Client">
                                            <i data-feather="user-x" class="w-4 h-4"></i>
                                        </button>
                                    @else
                                        <button onclick="toggleClientStatus({{ $client->id }}, true)" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Activate Client">
                                            <i data-feather="user-check" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="briefcase" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No clients found</p>
                                    <p class="text-sm">Get started by registering your first client.</p>
                                    <a href="{{ route('client-registration.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Register Client
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clients->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $clients->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $clients->firstItem() }}</span> to 
                            <span class="font-medium">{{ $clients->lastItem() }}</span> of 
                            <span class="font-medium">{{ $clients->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Client Management System
class ClientManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterClients());

        // Region filter
        const regionFilter = document.getElementById('regionFilter');
        regionFilter.addEventListener('change', () => this.filterClients());

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', () => this.filterClients());
    }

    filterClients() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const regionFilter = document.getElementById('regionFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const clientRows = document.querySelectorAll('.client-row');

        clientRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const region = row.dataset.region;
            const status = row.dataset.status;

            const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRegion = !regionFilter || region === regionFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesRegion && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

// Toggle client status function
async function toggleClientStatus(clientId, activate) {
    const action = activate ? 'activate' : 'deactivate';
    
    if (!confirm(`Are you sure you want to ${action} this client?`)) {
        return;
    }

    try {
        const response = await fetch(`/client-registration/${clientId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Status toggle error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize client manager
document.addEventListener('DOMContentLoaded', function() {
    window.clientManager = new ClientManager();
});
</script>
@endpush
