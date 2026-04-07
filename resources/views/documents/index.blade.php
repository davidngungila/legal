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

    <!-- Featured Documents -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Featured Documents</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Employment Contract -->
            @if(isset($groupedDocuments['contract']) && $groupedDocuments['contract']->count() > 0)
                @php($contract = $groupedDocuments['contract']->first())
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
            @if(isset($groupedDocuments['handbook']) && $groupedDocuments['handbook']->count() > 0)
                @php($handbook = $groupedDocuments['handbook']->first())
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
            @if(isset($groupedDocuments['policy']) && $groupedDocuments['policy']->count() > 0)
                @php($policy = $groupedDocuments['policy']->where('title', 'like', '%Code of Conduct%')->first() ?? $groupedDocuments['policy']->first())
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
            @if(isset($groupedDocuments['safety']) && $groupedDocuments['safety']->count() > 0)
                @php($safety = $groupedDocuments['safety']->first())
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
                    <option value="HR">HR</option>
                    <option value="Safety">Safety</option>
                    <option value="IT">IT</option>
                    <option value="Finance">Finance</option>
                    <option value="Operations">Operations</option>
                </select>
                <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="contract">Contracts</option>
                    <option value="handbook">Handbooks</option>
                    <option value="policy">Policies</option>
                    <option value="safety">Safety</option>
                    <option value="form">Forms</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="documentsTable">
                    @if($documents->count() > 0)
                        @foreach($documents as $document)
                        <tr class="hover:bg-gray-50">
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
                                {{ $document->category }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->version }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $document->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->formatted_file_size }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ $document->view_url }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                <a href="{{ $document->download_url }}" class="text-gray-600 hover:text-gray-900">Download</a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
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

@push('scripts')
<script>
// Document search
document.getElementById('documentSearch').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#documentsTable tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
});

// Category filter
document.getElementById('categoryFilter').addEventListener('change', function(e) {
    const category = e.target.value;
    const rows = document.querySelectorAll('#documentsTable tr');
    
    rows.forEach(row => {
        if (category === '') {
            row.style.display = '';
        } else {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(category.toLowerCase()) ? '' : 'none';
        }
    });
});

// Type filter
document.getElementById('typeFilter').addEventListener('change', function(e) {
    const type = e.target.value;
    const rows = document.querySelectorAll('#documentsTable tr');
    
    rows.forEach(row => {
        if (type === '') {
            row.style.display = '';
        } else {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(type.toLowerCase()) ? '' : 'none';
        }
    });
});

// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endpush

@endsection
