@extends('layouts.app')

@section('title', $document->title . ' - LegalHR Tanzania')

@php
    $hasPdfFile = $document->file_path
        && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)
        && strtolower($document->file_type ?? '') === 'pdf';
    $pdfUrl = $hasPdfFile ? \Illuminate\Support\Facades\Storage::disk('public')->url($document->file_path) : null;
@endphp

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
            @if($pdfUrl)
            <button onclick="openPdfPreview()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center space-x-2">
                <i data-feather="eye" class="w-4 h-4"></i>
                <span>Preview PDF</span>
            </button>
            @endif
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
                @if($pdfUrl)
                <div class="p-4">
                    <div class="w-full h-[70vh] rounded-lg overflow-hidden border border-gray-200">
                        <iframe src="{{ $pdfUrl }}" class="w-full h-full border-0" title="{{ $document->title }}"></iframe>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500">Preview of the uploaded PDF.</p>
                        <a href="{{ $pdfUrl }}" target="_blank" class="inline-flex items-center space-x-2 text-purple-600 hover:text-purple-800 text-sm font-medium">
                            <i data-feather="external-link" class="w-4 h-4"></i>
                            <span>Open in new tab</span>
                        </a>
                    </div>
                </div>
                @else
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
                @endif
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

@if($pdfUrl)
<!-- Fullscreen PDF Preview Modal -->
<div id="pdfPreviewModal" class="hidden fixed inset-0 z-[60] bg-black/60 items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[95vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-3">
                <i data-feather="eye" class="w-5 h-5 text-purple-600"></i>
                <h3 class="font-semibold text-gray-900">{{ $document->title }}</h3>
            </div>
            <button onclick="closePdfPreview()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-feather="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>
        <div class="p-4 overflow-hidden flex-1">
            <iframe src="{{ $pdfUrl }}" class="w-full h-full min-h-[60vh] border-0 rounded-lg" title="{{ $document->title }}"></iframe>
        </div>
        <div class="px-6 py-3 border-t border-gray-100 flex justify-end space-x-3 flex-shrink-0">
            <button onclick="closePdfPreview()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
            <a href="{{ $document->download_url }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                <i data-feather="download" class="w-4 h-4"></i>
                <span>Download PDF</span>
            </a>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openPdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('pdfPreviewModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePdfPreview();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePdfPreview();
    });
</script>
@endpush
@endsection
