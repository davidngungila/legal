@extends('layouts.app')

@section('title', 'Documents - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Documents</h1>
            <p class="text-gray-600 mt-2">Manage disciplinary case documents</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openDocumentModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                <span>Add Document</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $documents->total() }}</h3>
            <p class="text-gray-600 text-sm">Total Documents</p>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $doc->doc_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $doc->disciplinaryCase->case_number ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $doc->disciplinaryCase->employee->first_name ?? 'N/A' }} {{ $doc->disciplinaryCase->employee->last_name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $doc->generated_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">No disciplinary documents found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>

    <!-- Add Document Modal -->
    <x-advanced-modal id="documentModal" title="Add Document" icon="file-text" color="indigo" size="lg">
        <form id="documentForm" action="{{ route('discipline.documents.store') }}" method="POST">
            @csrf
            @php
                $clientId = session('current_client_id');
                $availableCases = $clientId ? \App\Models\DisciplinaryCase::with(['employee'])->where('client_id', $clientId)->get() : collect();
            @endphp
            <div class="space-y-4">
                <div>
                    <label for="docCaseId" class="block text-sm font-medium text-gray-700">Case</label>
                    <select id="docCaseId" name="case_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select case</option>
                        @foreach($availableCases as $c)
                            <option value="{{ $c->id }}">{{ $c->case_number }} - {{ $c->employee->first_name }} {{ $c->employee->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="docType" class="block text-sm font-medium text-gray-700">Document Type</label>
                    <select id="docType" name="doc_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select type</option>
                        <option value="Show Cause Notice">Show Cause Notice</option>
                        <option value="Suspension Letter">Suspension Letter</option>
                        <option value="Warning Letter">Warning Letter</option>
                        <option value="Termination Letter">Termination Letter</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="filePath" class="block text-sm font-medium text-gray-700">File Path (Optional)</label>
                    <input type="text" id="filePath" name="file_path" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('documentModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" form="documentForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Document</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>
</div>

<script>
    feather.replace();

    function openDocumentModal() {
        openModal('documentModal');
    }
</script>
@endsection
