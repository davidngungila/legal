@extends('layouts.app')

@section('title', 'Data Backup - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Data Backup</h1>
            <p class="text-gray-600 mt-2">Create, restore and manage database and file backups</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button type="button" onclick="openModal('uploadBackupModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                <i data-feather="upload-cloud" class="w-4 h-4 inline mr-1"></i>Upload Backup
            </button>
            <div class="relative" id="createBackupWrap">
                <button type="button" id="createBackupBtn" onclick="openCreateDropdown()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="plus" class="w-4 h-4 inline mr-1"></i>Create Backup
                </button>
                <div id="createBackupDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-20">
                    <button type="button" onclick="createBackup('database')" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 transition-colors flex items-start space-x-3">
                        <i data-feather="database" class="w-4 h-4 text-indigo-600 mt-0.5"></i>
                        <span>
                            <span class="block text-sm font-medium text-gray-900">Database Only</span>
                            <span class="block text-xs text-gray-500">Full SQL dump of all tables</span>
                        </span>
                    </button>
                    <button type="button" onclick="createBackup('files')" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 transition-colors flex items-start space-x-3">
                        <i data-feather="folder" class="w-4 h-4 text-purple-600 mt-0.5"></i>
                        <span>
                            <span class="block text-sm font-medium text-gray-900">Files Only</span>
                            <span class="block text-xs text-gray-500">Zip of uploads &amp; private storage</span>
                        </span>
                    </button>
                    <button type="button" onclick="createBackup('full')" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 transition-colors flex items-start space-x-3">
                        <i data-feather="archive" class="w-4 h-4 text-green-600 mt-0.5"></i>
                        <span>
                            <span class="block text-sm font-medium text-gray-900">Full Backup</span>
                            <span class="block text-xs text-gray-500">Database + files together</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="backupLoading" class="hidden fixed inset-0 z-[100] bg-black/40 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center space-y-4 max-w-sm mx-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            <p class="text-lg font-semibold text-gray-900" id="backupLoadingText">Creating backup...</p>
            <p class="text-sm text-gray-500 text-center">Please wait, this may take a few moments. Do not close this page.</p>
        </div>
    </div>

    <!-- Storage Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="database" class="w-5 h-5 text-blue-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['count'] }}</p>
            <p class="text-sm text-gray-500">Total Backups</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="hard-drive" class="w-5 h-5 text-purple-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['human_total_size'] }}</p>
            <p class="text-sm text-gray-500">Total Size</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="clock" class="w-5 h-5 text-green-600"></i>
            </div>
            @if($summary['last_backup'])
            <p class="text-lg font-bold text-gray-900 leading-tight">{{ \Carbon\Carbon::parse($summary['last_backup']['created_at'])->diffForHumans() }}</p>
            @else
            <p class="text-2xl font-bold text-gray-900">—</p>
            @endif
            <p class="text-sm text-gray-500">Last Backup</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="layers" class="w-5 h-5 text-amber-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['retention'] }}</p>
            <p class="text-sm text-gray-500">Retention Limit</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="calendar" class="w-5 h-5 text-teal-600"></i>
            </div>
            <p class="text-lg font-bold text-gray-900 leading-tight">Daily 02:00</p>
            <p class="text-sm text-gray-500">Scheduled Backup</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="disc" class="w-5 h-5 text-cyan-600"></i>
            </div>
            @if($summary['disk_total'])
            <p class="text-lg font-bold text-gray-900 leading-tight">{{ $summary['human_disk_used'] }} / {{ $summary['human_disk_total'] }}</p>
            @else
            <p class="text-2xl font-bold text-gray-900">—</p>
            @endif
            <p class="text-sm text-gray-500">Storage Used</p>
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Backup History</h3>
                <p class="text-sm text-gray-500">{{ $summary['storage_path'] }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Backup</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center
                                    {{ $backup['type'] == 'database' ? 'bg-blue-100' : ($backup['type'] == 'files' ? 'bg-purple-100' : 'bg-green-100') }}">
                                    <i data-feather="{{ $backup['type'] == 'database' ? 'database' : ($backup['type'] == 'files' ? 'folder' : 'archive') }}" class="w-4 h-4
                                        {{ $backup['type'] == 'database' ? 'text-blue-600' : ($backup['type'] == 'files' ? 'text-purple-600' : 'text-green-600') }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $backup['filename'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $backup['database'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full uppercase
                                @if($backup['type'] == 'database') bg-blue-100 text-blue-800
                                @elseif($backup['type'] == 'files') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $backup['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($backup['type'] == 'files')
                                <span class="text-gray-500">—</span>
                            @else
                                {{ $backup['tables'] }} tables / {{ number_format($backup['rows']) }} rows
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup['human_size'] ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($backup['created_at'])->format('M d, Y H:i') }}
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($backup['created_at'])->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ isset($backup['uploaded']) && $backup['uploaded'] ? 'Uploaded' : ($creators[$backup['created_by']] ?? 'Scheduled / System') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('backups.download', $backup['filename']) }}" title="Download" class="text-blue-600 hover:text-blue-900 p-1">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                </a>
                                <button type="button" onclick="confirmRestore('{{ $backup['filename'] }}', '{{ $backup['type'] }}')" title="Restore" class="text-green-600 hover:text-green-900 p-1">
                                    <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="confirmDelete('{{ $backup['filename'] }}')" title="Delete" class="text-red-600 hover:text-red-900 p-1">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="database" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">No Backups Yet</h4>
                            <p class="text-sm text-gray-500 mb-4">Create your first backup to protect your data.</p>
                            <button type="button" onclick="createBackup('database')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                <i data-feather="plus" class="w-4 h-4 inline mr-1"></i>Create Database Backup
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Backup Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Retention -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Retention Policy</h3>
            <p class="text-sm text-gray-500 mb-4">Automatically remove backups beyond this limit after each new backup.</p>
            <form method="POST" action="{{ route('backups.clean') }}">
                @csrf
                <div class="flex space-x-2">
                    <input type="number" name="keep" min="1" max="100" value="{{ $summary['retention'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors text-sm whitespace-nowrap">Apply</button>
                </div>
            </form>
            <p class="text-xs text-gray-400 mt-3">Configure the default with the <code class="bg-gray-100 px-1 rounded">BACKUP_RETENTION</code> env variable.</p>
        </div>

        <!-- Schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Automatic Schedule</h3>
            <p class="text-sm text-gray-500 mb-4">Scheduled backups run automatically when the scheduler is active.</p>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center justify-between">
                    <span class="text-gray-700">Database backup</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Daily 02:00</span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-gray-700">Full backup</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Mon 03:00</span>
                </li>
            </ul>
            <div class="mt-4 bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-600 mb-1">Run the scheduler in production:</p>
                <code class="block text-xs text-gray-800 bg-white border border-gray-200 rounded px-2 py-1">* * * * * cd /path/to/app &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">About Backups</h3>
            <p class="text-sm text-gray-500 mb-4">Backups are stored inside the application storage.</p>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-center space-x-2"><i data-feather="database" class="w-4 h-4 text-blue-500"></i><span>SQL dumps include structure + data for every table</span></li>
                <li class="flex items-center space-x-2"><i data-feather="folder" class="w-4 h-4 text-purple-500"></i><span>File backups zip uploads and private storage</span></li>
                <li class="flex items-center space-x-2"><i data-feather="rotate-ccw" class="w-4 h-4 text-green-500"></i><span>Restore from SQL or ZIP backups</span></li>
                <li class="flex items-center space-x-2"><i data-feather="shield" class="w-4 h-4 text-amber-500"></i><span>All actions are recorded in the audit trail</span></li>
            </ul>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<x-advanced-modal id="uploadBackupModal" title="Upload Backup" icon="upload-cloud" color="indigo" size="md">
    <form action="{{ route('backups.upload') }}" method="POST" enctype="multipart/form-data" id="uploadBackupForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="backup_file" class="block text-sm font-medium text-gray-700 mb-2">Backup File (.sql or .zip)</label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors" id="uploadDropzone">
                    <i data-feather="upload-cloud" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-sm text-gray-600">Click to choose or drop a file here</p>
                    <p class="text-xs text-gray-400 mt-1" id="uploadFileName">No file selected</p>
                    <input type="file" name="backup_file" id="backup_file" accept=".sql,.zip" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                </div>
            </div>
            <p class="text-xs text-amber-600"><i data-feather="alert-triangle" class="w-3 h-3 inline mr-1"></i>Uploading does not restore automatically. Use the Restore action afterwards.</p>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('uploadBackupModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="uploadBackupForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Upload</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Restore Confirmation Modal -->
<x-advanced-modal id="restoreModal" title="Restore Backup" icon="rotate-ccw" color="green" size="md">
    <form method="POST" action="" id="restoreForm">
        @csrf
        <div class="space-y-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                <p class="font-semibold mb-1"><i data-feather="alert-triangle" class="w-4 h-4 inline mr-1"></i>Warning</p>
                <p id="restoreWarningText">Restoring this backup will <strong>overwrite existing data</strong>. This action cannot be undone.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">RESTORE</code> to confirm</label>
                <input type="text" id="restoreConfirmInput" placeholder="RESTORE" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm uppercase">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('restoreModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="button" id="restoreSubmitBtn" onclick="submitRestore()" disabled class="px-4 py-2 bg-green-600 text-white rounded-lg opacity-50 cursor-not-allowed transition-opacity">Restore Backup</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmInput = document.getElementById('restoreConfirmInput');
    const submitBtn = document.getElementById('restoreSubmitBtn');
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            const enabled = this.value.trim().toUpperCase() === 'RESTORE';
            submitBtn.disabled = !enabled;
            submitBtn.classList.toggle('opacity-50', !enabled);
            submitBtn.classList.toggle('cursor-not-allowed', !enabled);
        });
    }

    const fileInput = document.getElementById('backup_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            document.getElementById('uploadFileName').textContent = this.files.length ? this.files[0].name : 'No file selected';
        });
    }
});

function openCreateDropdown() {
    document.getElementById('createBackupDropdown').classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('createBackupWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('createBackupDropdown').classList.add('hidden');
    }
});

function createBackup(type) {
    document.getElementById('createBackupDropdown').classList.add('hidden');
    const names = { database: 'database', files: 'files', full: 'full' };
    document.getElementById('backupLoadingText').textContent = 'Creating ' + names[type] + ' backup...';
    document.getElementById('backupLoading').classList.remove('hidden');

    fetch('{{ route('backups.create') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type: type })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('backupLoading').classList.add('hidden');
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            showNotification(data.message || 'Backup failed', 'error');
        }
    })
    .catch(e => {
        document.getElementById('backupLoading').classList.add('hidden');
        showNotification('Backup failed: ' + e.message, 'error');
    });
}

function confirmRestore(filename, type) {
    document.getElementById('restoreForm').action = '/backups/' + encodeURIComponent(filename) + '/restore';
    document.getElementById('restoreWarningText').innerHTML =
        'Restoring this <strong>' + type.toUpperCase() + '</strong> backup will <strong>overwrite existing ' +
        (type === 'files' ? 'uploaded files' : 'database records') + '</strong>. This action cannot be undone.';
    document.getElementById('restoreConfirmInput').value = '';
    document.getElementById('restoreSubmitBtn').disabled = true;
    document.getElementById('restoreSubmitBtn').classList.add('opacity-50', 'cursor-not-allowed');
    openModal('restoreModal');
    if (typeof feather !== 'undefined') feather.replace();
}

function submitRestore() {
    const btn = document.getElementById('restoreSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Restoring...';

    fetch(document.getElementById('restoreForm').action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        closeModal('restoreModal');
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Restore failed', 'error');
        }
        btn.textContent = 'Restore Backup';
    })
    .catch(e => {
        closeModal('restoreModal');
        btn.textContent = 'Restore Backup';
        showNotification('Restore failed: ' + e.message, 'error');
    });
}

function confirmDelete(filename) {
    if (!confirm('Are you sure you want to delete this backup? This action cannot be undone.')) return;

    fetch('/backups/' + encodeURIComponent(filename), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 600);
        } else {
            showNotification(data.message || 'Delete failed', 'error');
        }
    })
    .catch(e => showNotification('Delete failed: ' + e.message, 'error'));
}
</script>
@endsection
