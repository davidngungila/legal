@extends('layouts.app')

@section('title', $document->title . ' - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('documents.index') }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-manrope">{{ $document->title }}</h1>
                <p class="text-gray-600 mt-1 text-sm">Documents &amp; Policies</p>
            </div>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ $document->download_url }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Download PDF</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Document Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i data-feather="{{ $document->icon }}" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Document Preview</h3>
                            <p class="text-sm text-gray-500">{{ strtoupper($document->file_type ?? 'pdf') }} &middot; {{ $document->formatted_file_size }}</p>
                        </div>
                    </div>
                    {!! $document->status_badge !!}
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-8">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 mx-auto bg-indigo-100 rounded-2xl flex items-center justify-center mb-4">
                                <i data-feather="{{ $document->icon }}" class="w-10 h-10 text-indigo-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $document->title }}</h2>
                            <p class="text-gray-500 text-sm">Version {{ $document->version }}</p>
                        </div>
                        <p class="text-gray-700 text-center leading-relaxed mb-8">{{ $document->description }}</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Category</p>
                                <p class="font-medium text-gray-900">{{ $document->category ?? 'General' }}</p>
                            </div>
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Type</p>
                                <p class="font-medium text-gray-900">{{ ucfirst($document->document_type) }}</p>
                            </div>
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Effective Date</p>
                                <p class="font-medium text-gray-900">{{ $document->effective_date ? $document->effective_date->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Expiry Date</p>
                                <p class="font-medium text-gray-900">{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'No expiry' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <p class="text-sm text-gray-500">Preview generated from the current published version.</p>
                        <a href="{{ $document->download_url }}" class="inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            <i data-feather="download" class="w-4 h-4"></i>
                            <span>Download full document</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Details -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Document Details</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Document Type</dt>
                        <dd class="font-medium text-gray-900">{{ ucfirst($document->document_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Version</dt>
                        <dd class="font-medium text-gray-900">v{{ $document->version }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Category</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('documents.category', $document->category ?? 'general') }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $document->category ?? 'General' }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">File Size</dt>
                        <dd class="font-medium text-gray-900">{{ $document->formatted_file_size }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Effective Date</dt>
                        <dd class="font-medium text-gray-900">{{ $document->effective_date ? $document->effective_date->format('F j, Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Expiry Date</dt>
                        <dd class="font-medium text-gray-900">{{ $document->expiry_date ? $document->expiry_date->format('F j, Y') : 'No expiry' }}</dd>
                    </div>
                </dl>
            </div>

            @if(!empty($document->tags))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($document->tags as $tag)
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-indigo-50 rounded-xl border border-indigo-100 p-6">
                <div class="flex items-start space-x-3">
                    <i data-feather="info" class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-semibold text-indigo-900 text-sm">About this document</h4>
                        <p class="text-indigo-800/80 text-sm mt-1 leading-relaxed">
                            This {{ $document->document_type }} is {{ $document->is_required ? 'a mandatory' : 'an optional' }} document for
                            {{ $document->is_public ? 'all employees' : 'authorized personnel' }}. Please download the PDF for the complete official version.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
