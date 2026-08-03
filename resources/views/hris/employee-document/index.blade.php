@extends('layouts.app')

@section('title', 'Employee Document Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Document Management</h1>
            <p class="text-gray-600 mt-2">Manage and verify employee documents</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showUploadModal()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="upload" class="w-4 h-4 mr-2"></i>
                Upload Document
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Documents</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalDocs">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Verified</p>
                    <p class="text-2xl font-semibold text-gray-900" id="verifiedDocs">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900" id="pendingDocs">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Expired</p>
                    <p class="text-2xl font-semibold text-gray-900" id="expiredDocs">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                    <p class="text-2xl font-semibold text-gray-900" id="expiringSoonDocs">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search documents..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="employeeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->surname }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="national_id">National ID</option>
                    <option value="passport">Passport</option>
                    <option value="birth_certificate">Birth Certificate</option>
                    <option value="academic_certificate">Academic Certificate</option>
                    <option value="professional_certificate">Professional Certificate</option>
                    <option value="medical_certificate">Medical Certificate</option>
                    <option value="police_clearance">Police Clearance</option>
                    <option value="reference_letter">Reference Letter</option>
                    <option value="resume_cv">Resume/CV</option>
                    <option value="contract">Employment Contract</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="uploaded">Uploaded</option>
                    <option value="pending_verification">Pending Verification</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div>
                <select id="attentionFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Documents</option>
                    <option value="requiring_attention">Requiring Attention</option>
                    <option value="expiring_soon">Expiring Soon</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Document Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Document Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="documentsTableBody">
                    @forelse($documents as $document)
                        <tr class="hover:bg-gray-50 transition-colors document-row" 
                            data-name="{{ $document->document_name }}"
                            data-employee="{{ $document->employee->first_name . ' ' . $document->employee->surname }}"
                            data-type="{{ $document->document_type }}"
                            data-status="{{ $document->status }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <i data-feather="file" class="w-5 h-5 text-indigo-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->document_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $document->getDocumentTypeLabel() }}</div>
                                        <div class="text-xs text-gray-400">{{ $document->getFileSizeFormatted() }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $document->employee->first_name }} {{ $document->employee->surname }}</div>
                                <div class="text-sm text-gray-500">{{ $document->employee->employee_number }}</div>
                                <div class="text-sm text-gray-500">{{ $document->employee->work_station }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $document->document_number }}</div>
                                <div class="text-sm text-gray-500">{{ $document->issuing_authority }}</div>
                                @if($document->verified_at)
                                    <div class="text-xs text-green-600">Verified by {{ $document->verifier->name ?? 'Unknown' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($document->issue_date)
                                        Issued: {{ $document->issue_date->format('d M Y') }}
                                    @else
                                        No issue date
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($document->expiry_date)
                                        Expires: {{ $document->expiry_date->format('d M Y') }}
                                        @if($document->isExpired())
                                            <span class="text-red-600 font-medium">(Expired)</span>
                                        @elseif($document->isExpiringSoon())
                                            <span class="text-yellow-600 font-medium">({{ $document->getDaysUntilExpiry() }} days)</span>
                                        @endif
                                    @else
                                        No expiry date
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $document->getStatusColor() }}-100 text-{{ $document->getStatusColor() }}-800">
                                    {{ $document->getStatusLabel() }}
                                </span>
                                @if($document->is_required)
                                    <div class="text-xs text-orange-600 mt-1">Required</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('employee-document.show', $document) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('employee-document.download', $document) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </a>
                                    @if($document->canBeVerified())
                                        <button onclick="verifyDocument({{ $document->id }})" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Verify">
                                            <i data-feather="check-circle" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="rejectDocument({{ $document->id }})" 
                                                class="text-red-600 hover:text-red-900"
                                                title="Reject">
                                            <i data-feather="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    <button onclick="deleteDocument({{ $document->id }})" 
                                            class="text-gray-600 hover:text-gray-900"
                                            title="Delete">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No documents found</p>
                                    <p class="text-sm">Get started by uploading your first employee document.</p>
                                    <button onclick="showUploadModal()" 
                                            class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="upload" class="w-4 h-4 mr-2"></i>
                                        Upload Document
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $documents->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $documents->firstItem() }}</span> to 
                            <span class="font-medium">{{ $documents->lastItem() }}</span> of 
                            <span class="font-medium">{{ $documents->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<x-advanced-modal id="uploadModal" title="Upload Employee Document"
    icon="upload" color="green" size="md">
    <form id="uploadForm" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee <span class="text-red-500">*</span></label>
            <select name="employee_registration_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->surname }} ({{ $employee->employee_number }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label>
            <select name="document_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select Type</option>
                <option value="national_id">National ID</option>
                <option value="passport">Passport</option>
                <option value="birth_certificate">Birth Certificate</option>
                <option value="academic_certificate">Academic Certificate</option>
                <option value="professional_certificate">Professional Certificate</option>
                <option value="medical_certificate">Medical Certificate</option>
                <option value="police_clearance">Police Clearance</option>
                <option value="reference_letter">Reference Letter</option>
                <option value="resume_cv">Resume/CV</option>
                <option value="contract">Employment Contract</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Name <span class="text-red-500">*</span></label>
            <input type="text" name="document_name" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Number</label>
            <input type="text" name="document_number"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Issuing Authority</label>
            <input type="text" name="issuing_authority"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                <input type="date" name="issue_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document File <span class="text-red-500">*</span></label>
            <input type="file" name="document_file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="is_required" id="is_required"
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="is_required" class="ml-2 block text-sm text-gray-900">
                Required Document
            </label>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideUploadModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="uploadForm" id="uploadBtn"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <span id="uploadBtnText">Upload</span>
                <div id="uploadBtnLoader" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
// Employee Document Management System
class EmployeeDocumentManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
        this.loadStatistics();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterDocuments());

        // Filter functionality
        const filters = ['employeeFilter', 'typeFilter', 'statusFilter', 'attentionFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterDocuments());
        });

        // Upload form
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.uploadDocument();
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/employee-document/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;
                document.getElementById('totalDocs').textContent = stats.total_documents;
                document.getElementById('verifiedDocs').textContent = stats.verified_documents;
                document.getElementById('pendingDocs').textContent = stats.pending_verification;
                document.getElementById('expiredDocs').textContent = stats.expired_documents;
                document.getElementById('expiringSoonDocs').textContent = stats.expiring_soon;
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    filterDocuments() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const employeeFilter = document.getElementById('employeeFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const attentionFilter = document.getElementById('attentionFilter').value;
        const documentRows = document.querySelectorAll('.document-row');

        documentRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const employee = row.dataset.employee.toLowerCase();
            const type = row.dataset.type;
            const status = row.dataset.status;

            const matchesSearch = !searchTerm || name.includes(searchTerm) || employee.includes(searchTerm);
            const matchesEmployee = !employeeFilter || employee.includes(employeeFilter);
            const matchesType = !typeFilter || type === typeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            let matchesAttention = true;
            if (attentionFilter === 'requiring_attention') {
                matchesAttention = status === 'uploaded' || status === 'pending_verification';
            } else if (attentionFilter === 'expiring_soon') {
                // This would need to be implemented based on expiry dates
                matchesAttention = false; // Placeholder
            } else if (attentionFilter === 'expired') {
                matchesAttention = status === 'expired';
            }

            if (matchesSearch && matchesEmployee && matchesType && matchesStatus && matchesAttention) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async uploadDocument() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);

        this.setUploadLoadingState(true);

        try {
            const response = await fetch('/employee-document', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Document uploaded successfully', 'success');
                hideUploadModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                if (result.errors) {
                    this.showNotification('Validation failed. Please check the form.', 'error');
                } else {
                    this.showNotification(result.message || 'Upload failed', 'error');
                }
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification('An error occurred during upload', 'error');
        } finally {
            this.setUploadLoadingState(false);
        }
    }

    setUploadLoadingState(loading) {
        const btnText = document.getElementById('uploadBtnText');
        const btnLoader = document.getElementById('uploadBtnLoader');
        const uploadBtn = document.getElementById('uploadBtn');

        if (loading) {
            btnText.textContent = 'Uploading...';
            btnLoader.classList.remove('hidden');
            uploadBtn.disabled = true;
        } else {
            btnText.textContent = 'Upload';
            btnLoader.classList.add('hidden');
            uploadBtn.disabled = false;
        }
    }

    showNotification(message, type = 'info') {
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
}

// Modal functions
function showUploadModal() {
    openModal('uploadModal');
}

function hideUploadModal() {
    closeModal('uploadModal');
    document.getElementById('uploadForm').reset();
}

// Document action functions
async function verifyDocument(documentId) {
    if (!confirm('Are you sure you want to verify this document?')) {
        return;
    }

    try {
        const response = await fetch(`/employee-document/${documentId}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            window.employeeDocumentManager.showNotification('Document verified successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.employeeDocumentManager.showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Document verification error:', error);
        window.employeeDocumentManager.showNotification('An error occurred during the operation', 'error');
    }
}

async function rejectDocument(documentId) {
    const reason = prompt('Please provide a reason for rejection:');
    
    if (!reason) {
        return;
    }

    try {
        const response = await fetch(`/employee-document/${documentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        const result = await response.json();

        if (result.success) {
            window.employeeDocumentManager.showNotification('Document rejected successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.employeeDocumentManager.showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Document rejection error:', error);
        window.employeeDocumentManager.showNotification('An error occurred during the operation', 'error');
    }
}

async function deleteDocument(documentId) {
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/employee-document/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            window.employeeDocumentManager.showNotification('Document deleted successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.employeeDocumentManager.showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Document deletion error:', error);
        window.employeeDocumentManager.showNotification('An error occurred during the operation', 'error');
    }
}

// Initialize employee document manager
document.addEventListener('DOMContentLoaded', function() {
    window.employeeDocumentManager = new EmployeeDocumentManager();
});
</script>
@endpush
