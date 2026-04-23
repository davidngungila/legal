@extends('layouts.app')

@section('title', 'Upload Payroll CSV - LegalHR Tanzania')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Upload Payroll CSV</h1>
                <p class="text-gray-600 mt-1">Upload payroll data for {{ $client->name }}</p>
            </div>
            <a href="{{ route('payroll.index') }}" class="btn-transition bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Payroll
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Upload Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 hover-card">
                <h2 class="text-lg font-semibold mb-4">Upload CSV File</h2>
                
                <form id="payroll-upload-form" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Payroll Period <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="payroll_period" 
                               id="payroll_period"
                               class="form-input-transition w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., January 2024, Week 1, Q1 2024"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Specify the payroll period (e.g., "January 2024")</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pay Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="pay_date" 
                               id="pay_date"
                               class="form-input-transition w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CSV File <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                            <input type="file" 
                                   name="csv_file" 
                                   id="csv_file"
                                   accept=".csv,.txt"
                                   class="hidden"
                                   required>
                            <label for="csv_file" class="cursor-pointer">
                                <i data-feather="upload-cloud" class="w-12 h-12 mx-auto text-gray-400 mb-3"></i>
                                <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">CSV files only (Max 10MB)</p>
                            </label>
                            <div id="file-info" class="mt-4 hidden">
                                <p class="text-sm text-green-600 font-medium">
                                    <i data-feather="file-text" class="w-4 h-4 inline mr-1"></i>
                                    <span id="file-name"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" 
                                class="btn-transition flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="upload-btn">
                            <i data-feather="upload" class="w-4 h-4 mr-2"></i>
                            Upload Payroll Data
                        </button>
                        <a href="{{ route('payroll.template') }}" 
                           class="btn-transition bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i>
                            Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 hover-card">
                <h3 class="text-lg font-semibold mb-4">Instructions</h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">CSV Format Requirements:</h4>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li>File must be in CSV format</li>
                            <li>First row should contain column headers</li>
                            <li>Maximum file size: 10MB</li>
                            <li>Employee identification required (ID or email)</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Required Columns:</h4>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li><code class="bg-gray-100 px-1 rounded">employee_id</code> OR <code class="bg-gray-100 px-1 rounded">email</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">basic_salary</code></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Optional Columns:</h4>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li><code class="bg-gray-100 px-1 rounded">first_name</code>, <code class="bg-gray-100 px-1 rounded">last_name</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">overtime_hours</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">overtime_rate</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">overtime_pay</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">allowances</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">bonuses</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">tax_deductions</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">social_security</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">pension</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">other_deductions</code></li>
                            <li><code class="bg-gray-100 px-1 rounded">notes</code></li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i data-feather="info" class="w-4 h-4 inline mr-1"></i>
                            Employee Matching
                        </h4>
                        <p class="text-sm text-blue-800">
                            The system will match employees using:
                        </p>
                        <ol class="text-sm text-blue-800 mt-2 list-decimal list-inside">
                            <li>Employee ID (preferred)</li>
                            <li>Email address</li>
                            <li>First + Last name combination</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-card mt-6">
                <h3 class="text-lg font-semibold mb-4">Recent Uploads</h3>
                <div id="recent-uploads" class="space-y-2">
                    <p class="text-sm text-gray-500">No recent uploads</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div id="results-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Upload Results</h3>
            <div id="results-content"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeResultsModal()" class="btn-transition bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    const form = document.getElementById('payroll-upload-form');
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const uploadBtn = document.getElementById('upload-btn');
    
    // File selection handling
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileInfo.classList.remove('hidden');
        } else {
            fileInfo.classList.add('hidden');
        }
    });
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> Uploading...';
        
        try {
            const response = await fetch('{{ route("payroll.upload.csv") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showResults(result);
                form.reset();
                fileInfo.classList.add('hidden');
                
                // Refresh recent uploads
                loadRecentUploads();
            } else {
                showNotification(result.message || 'Upload failed', 'error');
            }
        } catch (error) {
            showNotification('Network error. Please try again.', 'error');
        } finally {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i data-feather="upload" class="w-4 h-4 mr-2"></i> Upload Payroll Data';
            feather.replace();
        }
    });
    
    // Load recent uploads
    loadRecentUploads();
});

function showResults(result) {
    const modal = document.getElementById('results-modal');
    const content = document.getElementById('results-content');
    
    let html = `
        <div class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-medium text-green-900 mb-2">Upload Summary</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-green-700 font-medium">Processed:</span>
                        <span class="text-green-900 ml-2">${result.processed}</span>
                    </div>
                    <div>
                        <span class="text-yellow-700 font-medium">Skipped:</span>
                        <span class="text-yellow-900 ml-2">${result.skipped}</span>
                    </div>
                </div>
            </div>
    `;
    
    if (result.errors && result.errors.length > 0) {
        html += `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-medium text-yellow-900 mb-2">Warnings/Errors</h4>
                <div class="max-h-60 overflow-y-auto">
                    <ul class="text-sm text-yellow-800 space-y-1">
        `;
        
        result.errors.forEach(error => {
            html += `<li>${error}</li>`;
        });
        
        html += `
                    </ul>
                </div>
            </div>
        `;
    }
    
    html += `
        </div>
    `;
    
    content.innerHTML = html;
    modal.classList.remove('hidden');
}

function closeResultsModal() {
    document.getElementById('results-modal').classList.add('hidden');
}

function loadRecentUploads() {
    // This would typically load from an API endpoint
    // For now, show placeholder
    const container = document.getElementById('recent-uploads');
    container.innerHTML = '<p class="text-sm text-gray-500">No recent uploads</p>';
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification-slide fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'notificationSlide 0.4s ease-out reverse';
        setTimeout(() => notification.remove(), 400);
    }, 5000);
}
</script>
@endsection
