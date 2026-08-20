@extends('layouts.app')

@section('title', ucfirst($type) . ' Documents - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('documents.index') }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">{{ ucfirst($type) }} Documents</h1>
                <p class="text-gray-600 mt-2">{{ $documents->count() }} document(s) of this type</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('documents.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Back to All Documents</a>
        </div>
    </div>

    @include('documents._doc-grid', ['documents' => $documents])
</div>
@endsection
