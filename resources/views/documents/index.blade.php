@extends('layouts.app')

@section('title', 'Documents & Policies - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Documents & Policies</h1>
            <p class="text-gray-600 mt-2">View All</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <div class="relative">
                <input type="text" id="documentSearch" placeholder="Search documents..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
            </div>
            <button onclick="openCreateDocumentModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Document</span>
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500">All Documents</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Contracts</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['contracts'] }}</p>
            <p class="text-sm text-gray-500">Employment Agreements</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="book" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Handbooks</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['handbooks'] }}</p>
            <p class="text-sm text-gray-500">Employee Guides</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">Policies</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['policies'] }}</p>
            <p class="text-sm text-gray-500">Company Policies</p>
        </div>
    </div>

    <!-- Browse by Category -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Browse by Category</h2>
        <div class="flex flex-wrap gap-3">
            @php($categories = $documents->groupBy('category')->sortKeys())
            @foreach($categories as $catName => $catDocs)
                @php($catSlug = $catName ?: 'general')
                <a href="{{ route('documents.category', $catSlug) }}" class="flex items-center space-x-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all">
                    <span class="w-2 h-2 rounded-full {{ $loop->first ? 'bg-blue-500' : ($loop->iteration == 2 ? 'bg-green-500' : ($loop->iteration == 3 ? 'bg-orange-500' : 'bg-purple-500')) }}"></span>
                    <span class="text-sm font-medium text-gray-800">{{ $catName ?: 'General' }}</span>
                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">{{ $catDocs->count() }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Featured Documents -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Featured Documents</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Employment Contract -->
            @php($contract = $publicDocuments->where('document_type', 'contract')->first())
            @if($contract)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-feather="{{ $contract->icon }}" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    {!! $contract->status_badge !!}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $contract->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($contract->description, 80) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $contract->formatted_file_size }}</span>
                    <a href="{{ $contract->view_url }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Document</a>
                </div>
            </div>
            @endif

            <!-- Employee Handbook -->
            @php($handbook = $publicDocuments->where('document_type', 'handbook')->first())
            @if($handbook)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-feather="{{ $handbook->icon }}" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    {!! $handbook->status_badge !!}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $handbook->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($handbook->description, 80) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $handbook->formatted_file_size }}</span>
                    <a href="{{ $handbook->view_url }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Document</a>
                </div>
            </div>
            @endif

            <!-- Code of Conduct -->
            @php($policy = $publicDocuments->where('document_type', 'policy')->where('title', 'like', '%Code of Conduct%')->first() ?? $publicDocuments->where('document_type', 'policy')->first())
            @if($policy)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-feather="{{ $policy->icon }}" class="w-6 h-6 text-green-600"></i>
                    </div>
                    {!! $policy->status_badge !!}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $policy->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($policy->description, 80) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $policy->formatted_file_size }}</span>
                    <a href="{{ $policy->view_url }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Document</a>
                </div>
            </div>
            @endif

            <!-- Safety Policy -->
            @php($safety = $publicDocuments->where('document_type', 'safety')->first())
            @if($safety)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i data-feather="{{ $safety->icon }}" class="w-6 h-6 text-orange-600"></i>
                    </div>
                    {!! $safety->status_badge !!}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $safety->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($safety->description, 80) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $safety->formatted_file_size }}</span>
                    <a href="{{ $safety->view_url }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Document</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- All Documents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">All Documents</h2>
            <div class="flex items-center space-x-3">
                <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($documents->groupBy('category')->sortKeys() as $catName => $catDocs)
                        <option value="{{ strtolower($catName ?? '') }}">{{ $catName ?: 'General' }}</option>
                    @endforeach
                </select>
                <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    @foreach($documents->groupBy('document_type')->sortKeys() as $typeName => $typeDocs)
                        <option value="{{ strtolower($typeName) }}">{{ ucfirst($typeName) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="documentsTable">
                    @if($documents->count() > 0)
                        @foreach($documents as $document)
                        <tr class="hover:bg-gray-50" data-title="{{ strtolower($document->title) }}" data-category="{{ strtolower($document->category ?? '') }}" data-type="{{ strtolower($document->document_type) }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <i data-feather="{{ $document->icon }}" class="w-4 h-4 text-gray-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $document->title }}</p>
                                        <p class="text-sm text-gray-500">{{ Str::limit($document->description, 60) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    {{ ucfirst($document->document_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('documents.category', $document->category ?? 'general') }}" class="text-indigo-600 hover:text-indigo-800">
                                    {{ $document->category }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->version }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->effective_date ? $document->effective_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $document->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->formatted_file_size }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <button onclick="previewStoredDocument({{ $document->id }})" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Preview PDF">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </button>
                                <a href="{{ $document->view_url }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="View Details">
                                    <i data-feather="file-text" class="w-5 h-5"></i>
                                </a>
                                <a href="{{ $document->download_url }}" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Download">
                                    <i data-feather="download" class="w-5 h-5"></i>
                                </a>
                                <button onclick="openEditDocumentModal({{ $document->id }})" class="p-2 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </button>
                                <button onclick="deleteDocument({{ $document->id }})" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </td>
                        </tr>
                        @endforeach
                    @else
                        <tr id="documentsEmptyState">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i data-feather="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p>No documents found.</p>
                                <p class="text-sm mt-2">Documents will appear here once added by HR.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Document Modal -->
<div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900" id="documentModalTitle">Add Document</h3>
            <button onclick="closeDocumentModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="p-6 flex-1 overflow-y-auto">
            @include('documents.partials.document-form', ['document_types' => [
                'contract' => 'Contract',
                'handbook' => 'Handbook',
                'policy' => 'Policy',
                'safety' => 'Safety',
                'procedure' => 'Procedure',
                'form' => 'Form',
                'report' => 'Report',
                'other' => 'Other',
            ]])
        </div>
    </div>
</div>

@push('scripts')
<script>
const documentSearchInput = document.getElementById('documentSearch');
const categoryFilter = document.getElementById('categoryFilter');
const typeFilter = document.getElementById('typeFilter');

// Tag management
let tags = [];

function filterDocuments() {
    const query = documentSearchInput ? documentSearchInput.value.toLowerCase() : '';
    const category = categoryFilter ? categoryFilter.value : '';
    const type = typeFilter ? typeFilter.value : '';
    const rows = document.querySelectorAll('#documentsTable tr[data-title]');
    let visible = 0;

    rows.forEach(row => {
        const title = row.getAttribute('data-title');
        const rowCategory = row.getAttribute('data-category');
        const rowType = row.getAttribute('data-type');
        const show = (!query || title.includes(query))
            && (!category || rowCategory === category)
            && (!type || rowType === type);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const emptyState = document.getElementById('documentsEmptyState');
    if (emptyState) {
        emptyState.style.display = visible === 0 ? '' : 'none';
    }
}

if (documentSearchInput) documentSearchInput.addEventListener('input', filterDocuments);
if (categoryFilter) categoryFilter.addEventListener('change', filterDocuments);
if (typeFilter) typeFilter.addEventListener('change', filterDocuments);

// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}

// Document Modal Functions
function openCreateDocumentModal() {
    document.getElementById('documentModalTitle').textContent = 'Add Document';
    document.getElementById('documentForm').reset();
    document.getElementById('documentId').value = '';
    document.getElementById('replaceFile').value = 'false';
    document.getElementById('selectedFileInfo').classList.add('hidden');
    document.getElementById('existingFileInfo').classList.add('hidden');
    document.getElementById('replaceFileCheckbox').checked = false;
    document.getElementById('fileDropZone').classList.remove('hidden');
    tags = [];
    updateTagsDisplay();
    document.getElementById('tagsInput').value = '';
    document.getElementById('documentModal').classList.remove('hidden');
    document.getElementById('documentModal').classList.add('flex');
    feather.replace();
}

function openEditDocumentModal(documentId) {
    document.getElementById('documentModalTitle').textContent = 'Edit Document';
    document.getElementById('documentId').value = documentId;
    
    // Fetch document data
    fetch(`/documents/${documentId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const doc = data.document;
                document.getElementById('documentTitle').value = doc.title;
                document.getElementById('documentDescription').value = doc.description || '';
                document.getElementById('documentType').value = doc.document_type;
                document.getElementById('documentCategory').value = doc.category || '';
                document.getElementById('documentVersion').value = doc.version;
                document.getElementById('documentStatus').value = doc.status;
                document.getElementById('documentEffectiveDate').value = doc.effective_date ? doc.effective_date.split('T')[0] : '';
                document.getElementById('documentExpiryDate').value = doc.expiry_date ? doc.expiry_date.split('T')[0] : '';
                document.getElementById('documentIsRequired').checked = doc.is_required;
                document.getElementById('documentIsPublic').checked = doc.is_public;
                
                // Handle tags
                tags = doc.tags || [];
                updateTagsDisplay();
                
                // Handle file info
                if (doc.file_path) {
                    document.getElementById('fileDropZone').classList.add('hidden');
                    document.getElementById('existingFileInfo').classList.remove('hidden');
                    document.getElementById('existingFileName').textContent = doc.file_path.split('/').pop();
                    document.getElementById('existingFileSize').textContent = formatFileSize(doc.file_size);
                } else {
                    document.getElementById('fileDropZone').classList.remove('hidden');
                    document.getElementById('existingFileInfo').classList.add('hidden');
                }
                document.getElementById('selectedFileInfo').classList.add('hidden');
                document.getElementById('replaceFile').value = 'false';
                document.getElementById('replaceFileCheckbox').checked = false;
                
                document.getElementById('documentModal').classList.remove('hidden');
                document.getElementById('documentModal').classList.add('flex');
                feather.replace();
            } else {
                showNotification('Error: ' + data.error, 'error');
            }
        })
        .catch(error => {
            showNotification('Error: ' + error.message, 'error');
        });
}

function closeDocumentModal() {
    document.getElementById('documentModal').classList.add('hidden');
    document.getElementById('documentModal').classList.remove('flex');
}

// File upload handling
document.getElementById('documentFile')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        displaySelectedFile(file);
    }
});

// Drag and drop
const dropZone = document.getElementById('fileDropZone');
if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    dropZone.addEventListener('drop', handleDrop, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const file = dt.files[0];
    if (file) {
        document.getElementById('documentFile').files = dt.files;
        displaySelectedFile(file);
    }
}

function displaySelectedFile(file) {
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Invalid file type. Only PDF, DOC, DOCX allowed.', 'error');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showNotification('File size must be less than 10MB.', 'error');
        return;
    }
    
    document.getElementById('fileDropZone').classList.add('hidden');
    document.getElementById('selectedFileInfo').classList.remove('hidden');
    document.getElementById('existingFileInfo').classList.add('hidden');
    
    const icon = file.type === 'application/pdf' ? 'file-text' : 'file';
    document.getElementById('fileIcon').setAttribute('data-feather', icon);
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    feather.replace();
}

function clearFile() {
    document.getElementById('documentFile').value = '';
    document.getElementById('selectedFileInfo').classList.add('hidden');
    document.getElementById('fileDropZone').classList.remove('hidden');
    document.getElementById('existingFileInfo').classList.add('hidden');
}

// Preview the selected (not yet uploaded) file client-side
function previewSelectedFile() {
    const fileInput = document.getElementById('documentFile');
    const file = fileInput ? fileInput.files[0] : null;
    
    if (!file) {
        showNotification('No file selected.', 'error');
        return;
    }
    
    if (file.type !== 'application/pdf') {
        showNotification('Inline preview is only available for PDF files. DOC/DOCX will be viewable after saving.', 'info');
        return;
    }
    
    const url = URL.createObjectURL(file);
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = `
        <div class="w-full h-[70vh]">
            <iframe src="${url}" class="w-full h-full border-0 rounded-lg" title="${file.name}"></iframe>
        </div>
    `;
    
    document.getElementById('documentPreviewModal').classList.remove('hidden');
    document.getElementById('documentPreviewModal').classList.add('flex');
}

// Preview a saved document's stored PDF
async function previewStoredDocument(documentId) {
    try {
        const response = await fetch(`/documents/file-preview/${documentId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();
        
        if (!result.success) {
            showNotification('Error: ' + (result.error || 'Failed to load preview'), 'error');
            return;
        }
        
        const previewContent = document.getElementById('previewContent');
        
        if (result.type === 'pdf' && result.url) {
            previewContent.innerHTML = `
                <div class="w-full h-[70vh]">
                    <iframe src="${result.url}" class="w-full h-full border-0 rounded-lg" title="${result.title}"></iframe>
                </div>
            `;
        } else if (result.html) {
            previewContent.innerHTML = result.html;
        } else {
            previewContent.innerHTML = '<p class="text-center text-gray-500 py-8">No preview available.</p>';
        }
        
        document.getElementById('documentPreviewModal').classList.remove('hidden');
        document.getElementById('documentPreviewModal').classList.add('flex');
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function toggleReplaceFile() {
    const checked = document.getElementById('replaceFileCheckbox').checked;
    document.getElementById('replaceFile').value = checked;
    if (checked) {
        document.getElementById('fileDropZone').classList.remove('hidden');
        document.getElementById('existingFileInfo').classList.add('hidden');
    } else {
        document.getElementById('fileDropZone').classList.add('hidden');
        document.getElementById('existingFileInfo').classList.remove('hidden');
        document.getElementById('selectedFileInfo').classList.add('hidden');
        document.getElementById('documentFile').value = '';
    }
}

// Tag handling
document.getElementById('tagInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        e.preventDefault();
        const tag = this.value.trim();
        if (!tags.includes(tag)) {
            tags.push(tag);
            updateTagsDisplay();
        }
        this.value = '';
    }
});

function updateTagsDisplay() {
    const tagsList = document.getElementById('tagsList');
    tagsList.innerHTML = tags.map(tag => `
        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full flex items-center space-x-1">
            <span>${tag}</span>
            <button type="button" onclick="removeTag('${tag}')" class="text-indigo-500 hover:text-indigo-700">
                <i data-feather="x" class="w-3 h-3"></i>
            </button>
        </span>
    `).join('');
    document.getElementById('tagsInput').value = JSON.stringify(tags);
    feather.replace();
}

function removeTag(tag) {
    tags = tags.filter(t => t !== tag);
    updateTagsDisplay();
}

// Form submission
document.getElementById('documentForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const documentId = formData.get('document_id');
    const url = documentId ? `/documents/${documentId}` : '/documents';
    const method = documentId ? 'POST' : 'POST'; // Using POST with _method for PUT
    
    if (documentId) {
        formData.append('_method', 'PUT');
    }
    
    // Add tags
    formData.set('tags', JSON.stringify(tags));
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            closeDocumentModal();
            location.reload();
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                showNotification(firstError, 'error');
            } else {
                showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
});

// Preview document
async function previewDocument() {
    const formData = new FormData(document.getElementById('documentForm'));
    formData.set('tags', JSON.stringify(tags));
    
    // Include file for preview (don't delete it)
    
    try {
        const response = await fetch('/documents/preview', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            const previewContent = document.getElementById('previewContent');
            
            if (result.type === 'pdf' && result.url) {
                // Show PDF in iframe
                previewContent.innerHTML = `
                    <div class="w-full h-[70vh]">
                        <iframe src="${result.url}" class="w-full h-full border-0 rounded-lg" title="${result.title}"></iframe>
                    </div>
                `;
            } else if (result.html) {
                // Show HTML preview
                previewContent.innerHTML = result.html;
            }
            
            document.getElementById('documentPreviewModal').classList.remove('hidden');
            document.getElementById('documentPreviewModal').classList.add('flex');
        } else {
            let errorMsg = 'Error generating preview';
            if (result.error) {
                errorMsg += ': ' + result.error;
            } else if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                errorMsg += ': ' + firstError;
            } else {
                errorMsg += ': Unknown error';
            }
            showNotification(errorMsg, 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function closePreviewModal() {
    document.getElementById('documentPreviewModal').classList.add('hidden');
    document.getElementById('documentPreviewModal').classList.remove('flex');
    // Revoke any blob object URLs created for client-side previews
    document.querySelectorAll('#previewContent iframe').forEach(iframe => {
        if (iframe.src && iframe.src.startsWith('blob:')) {
            URL.revokeObjectURL(iframe.src);
        }
    });
    document.getElementById('previewContent').innerHTML = '';
}

// Delete document
async function deleteDocument(documentId) {
    if (!confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            location.reload();
        } else {
            showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Format file size
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Show notification
function showNotification(message, type = 'info') {
    const colors = {
        'success': 'bg-green-600',
        'error': 'bg-red-600',
        'info': 'bg-indigo-600',
        'warning': 'bg-yellow-600'
    };
    const color = colors[type] || colors.info;
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${color} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush

<!-- Document Preview Modal (top-level so it works from table rows and inside the form modal) -->
<div id="documentPreviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[60] overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-4 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Document Preview</h3>
            <button onclick="closePreviewModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4" id="previewContent">
            <!-- Preview content loaded here -->
        </div>
        <div class="p-4 border-t border-gray-200 flex-shrink-0 flex justify-end space-x-3">
            <button onclick="closePreviewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
        </div>
    </div>
</div>

@endsection
