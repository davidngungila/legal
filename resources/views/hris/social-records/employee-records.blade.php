@extends('layouts.app')

@section('title', 'Employee Social Records - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('social-records.index') }}" class="hover:text-indigo-600">Social Records</a>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
                <span>Employee Details</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Social Records: {{ $employee->first_name }} {{ $employee->surname }}</h1>
            <p class="text-gray-600 mt-2">Detailed social security, welfare and banking records for {{ $employee->employee_number }}</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.history.back()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back
            </button>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                Print Records
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Employee Info & Status -->
        <div class="space-y-6">
            <!-- Employee Quick Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                        <span class="text-indigo-600 font-bold text-3xl">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->surname, 0, 1)) }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->surname }}</h2>
                    <p class="text-gray-500">{{ $employee->employee_number }}</p>
                    <span class="mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
                
                <div class="mt-8 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Work Station:</span>
                        <span class="font-medium text-gray-900">{{ $employee->work_station }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Employment Date:</span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($employee->date_employed)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Record Status:</span>
                        <span class="font-medium {{ $socialRecord ? 'text-green-600' : 'text-red-600' }}">
                            {{ $socialRecord ? 'Registered' : 'Not Registered' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Documents Checklist -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-feather="file-text" class="w-5 h-5 mr-2 text-gray-400"></i>
                    Documents Checklist
                </h3>
                <div class="space-y-3">
                    @php
                        $docs = [
                            ['label' => 'NSSF Card', 'exists' => $socialRecord && $socialRecord->nssf_card_path],
                            ['label' => 'NHIF Card', 'exists' => $socialRecord && $socialRecord->nhif_card_path],
                            ['label' => 'TIN Certificate', 'exists' => $socialRecord && $socialRecord->tin_certificate_path],
                            ['label' => 'WCF Certificate', 'exists' => $socialRecord && $socialRecord->wcf_certificate_path],
                            ['label' => 'OSHA Certificate', 'exists' => $socialRecord && $socialRecord->osha_certificate_path],
                            ['label' => 'Bank Verification', 'exists' => $socialRecord && $socialRecord->bank_verification_path],
                        ];
                    @endphp

                    @foreach($docs as $doc)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <span class="text-sm text-gray-600">{{ $doc['label'] }}</span>
                            @if($doc['exists'])
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            @else
                                <i data-feather="x-circle" class="w-4 h-4 text-red-400"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Middle & Right Columns: Record Details -->
        <div class="lg:col-span-2 space-y-6">
            @if($socialRecord)
                <!-- Social Security & Tax -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center">
                            <i data-feather="shield" class="w-5 h-5 mr-2 text-indigo-600"></i>
                            Social Security & Taxation
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">NSSF Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->nssf_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">NHIF Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->nhif_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">TIN Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->tin_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">WCF Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->wcf_number }}</p>
                            </div>
                            @if($socialRecord->osha_number)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">OSHA Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->osha_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Banking Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900 flex items-center">
                            <i data-feather="credit-card" class="w-5 h-5 mr-2 text-green-600"></i>
                            Banking Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Bank Name</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->bank_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Account Number</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->bank_account_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Branch Name</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->bank_branch }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact & Next of Kin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Emergency Contact -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="font-bold text-gray-900 flex items-center">
                                <i data-feather="phone" class="w-5 h-5 mr-2 text-red-600"></i>
                                Emergency Contact
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->emergency_contact_name }}</p>
                            </div>
                            <div class="flex justify-between">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Relationship</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->emergency_contact_relationship }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Phone</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->emergency_contact_phone }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Address</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->emergency_contact_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Next of Kin -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="font-bold text-gray-900 flex items-center">
                                <i data-feather="users" class="w-5 h-5 mr-2 text-blue-600"></i>
                                Next of Kin
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->next_of_kin_name }}</p>
                            </div>
                            <div class="flex justify-between">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Relationship</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->next_of_kin_relationship }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Phone</label>
                                    <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->next_of_kin_phone }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase">Address</label>
                                <p class="text-gray-900 font-medium mt-1">{{ $socialRecord->next_of_kin_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                @if($socialRecord->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900 flex items-center">
                            <i data-feather="message-square" class="w-5 h-5 mr-2 text-gray-400"></i>
                            Additional Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-line">{{ $socialRecord->notes }}</p>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between text-xs text-gray-400 mt-8 pt-4 border-t border-gray-100">
                    <span>Record Created: {{ $socialRecord->created_at->format('M d, Y H:i') }}</span>
                    <span>Last Updated: {{ $socialRecord->updated_at->format('M d, Y H:i') }}</span>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-orange-100 p-4 rounded-full">
                            <i data-feather="alert-circle" class="w-12 h-12 text-orange-600"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Social Records Found</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        This employee does not have any social records registered in the system yet. Please complete the registration from the main dashboard.
                    </p>
                    <a href="{{ route('social-records.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Register Social Records
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush