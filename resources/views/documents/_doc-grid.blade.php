@if($documents->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($documents as $document)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i data-feather="{{ $document->icon }}" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <div class="flex items-center space-x-2">
                {!! $document->status_badge !!}
                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">v{{ $document->version }}</span>
            </div>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2">{{ $document->title }}</h3>
        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($document->description, 90) }}</p>
        <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">{{ ucfirst($document->document_type) }}</span>
            @if($document->category)
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ $document->category }}</span>
            @endif
        </div>
        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <div class="text-xs text-gray-500 space-y-0.5">
                <p class="flex items-center space-x-1">
                    <i data-feather="calendar" class="w-3 h-3"></i>
                    <span>{{ $document->effective_date ? $document->effective_date->format('M d, Y') : 'N/A' }}</span>
                </p>
                <p class="flex items-center space-x-1">
                    <i data-feather="hard-drive" class="w-3 h-3"></i>
                    <span>{{ $document->formatted_file_size }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ $document->view_url }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                <a href="{{ $document->download_url }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium" title="Download">
                    <i data-feather="download" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <i data-feather="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
    <p class="text-gray-600 font-medium">No documents found.</p>
    <p class="text-sm text-gray-500 mt-2">Documents will appear here once added by HR.</p>
</div>
@endif
