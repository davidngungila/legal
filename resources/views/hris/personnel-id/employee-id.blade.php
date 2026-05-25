@extends('layouts.app')

@section('title', 'Employee ID Applications - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('personnel-id.index') }}" class="hover:text-indigo-600">Personnel ID</a>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
                <span>ID Applications</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">ID Applications: {{ $employee->first_name }} {{ $employee->last_name }}</h1>
            <p class="text-gray-600 mt-2">Manage ID card applications and access credentials for {{ $employee->employee_id }}</p>
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
                Print History
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Column: Employee Profile & Quick Actions -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="relative inline-block mb-4">
                    @php
                        $latestApp = $applications->first();
                    @endphp
                    @if($latestApp && $latestApp->photo_path)
                        <img src="{{ Storage::url($latestApp->photo_path) }}" alt="Profile Photo" 
                             class="h-32 w-32 rounded-lg object-cover border-4 border-gray-50 shadow-sm">
                    @else
                        <div class="h-32 w-32 rounded-lg bg-indigo-100 flex items-center justify-center border-4 border-gray-50 shadow-sm">
                            <i data-feather="user" class="w-16 h-16 text-indigo-300"></i>
                        </div>
                    @endif
                    <div class="absolute -bottom-2 -right-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500 text-white shadow-sm ring-2 ring-white">
                            <i data-feather="check" class="w-3 h-3"></i>
                        </span>
                    </div>
                </div>
                
                <h2 class="text-lg font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $employee->position }}</p>

                <div class="mt-6 pt-6 border-t border-gray-100 space-y-4 text-left">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Department:</span>
                        <span class="font-bold text-gray-900">{{ $employee->department }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Gender:</span>
                        <span class="font-bold text-gray-900">{{ ucfirst($employee->gender) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Blood Group:</span>
                        <span class="font-bold text-red-600">O+</span> <!-- Example placeholder -->
                    </div>
                </div>
            </div>

            <!-- Access Levels -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                    <i data-feather="key" class="w-4 h-4 mr-2 text-indigo-500"></i>
                    Active Access Permissions
                </h3>
                <div class="space-y-2">
                    @if($latestApp)
                        <div class="flex items-center text-xs text-gray-600 bg-gray-50 p-2 rounded">
                            <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2"></i>
                            General Office Access
                        </div>
                        @if($latestApp->after_hours_access)
                        <div class="flex items-center text-xs text-gray-600 bg-gray-50 p-2 rounded">
                            <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2"></i>
                            After-Hours Access
                        </div>
                        @endif
                        @if($latestApp->emergency_access)
                        <div class="flex items-center text-xs text-gray-600 bg-gray-50 p-2 rounded">
                            <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2"></i>
                            Emergency Access
                        </div>
                        @endif
                    @else
                        <p class="text-xs text-gray-400 italic">No active permissions found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Application History -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($applications as $app)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Application Header -->
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between bg-gray-50/50">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                <i data-feather="credit-card" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $app->id_type)) }}</h3>
                                <p class="text-xs text-gray-500">ID Number: <span class="font-medium text-gray-700">{{ $app->id_number }}</span></p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center space-x-3">
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                @if($app->status === 'issued') bg-green-100 text-green-700 
                                @elseif($app->status === 'pending') bg-yellow-100 text-yellow-700 
                                @elseif($app->status === 'expired') bg-red-100 text-red-700 
                                @else bg-gray-100 text-gray-700 @endif uppercase">
                                {{ $app->status }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $app->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Left: ID Visual Preview -->
                            <div class="md:col-span-1">
                                <div class="w-full aspect-[1.586/1] bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl p-4 text-white shadow-lg relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>
                                    
                                    <div class="relative z-10 flex flex-col h-full justify-between">
                                        <div class="flex justify-between items-start">
                                            <div class="text-[10px] font-bold tracking-widest uppercase opacity-80">Orvion Legal HR</div>
                                            <div class="text-[8px] font-medium opacity-60">EMPLOYEE ID</div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-lg bg-white/20 backdrop-blur-sm border border-white/30 overflow-hidden">
                                                @if($app->photo_path)
                                                    <img src="{{ Storage::url($app->photo_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i data-feather="user" class="w-6 h-6 text-white/40"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-bold">{{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}</div>
                                                <div class="text-[8px] opacity-80">{{ strtoupper($employee->position) }}</div>
                                                <div class="text-[8px] font-mono mt-1">{{ $app->id_number }}</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-end border-t border-white/20 pt-2">
                                            <div class="text-[7px] opacity-60">EXP: {{ \Carbon\Carbon::parse($app->valid_until)->format('m/Y') }}</div>
                                            @if($app->signature_path)
                                                <img src="{{ Storage::url($app->signature_path) }}" class="h-4 w-auto grayscale invert opacity-80">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 text-center mt-3 italic">Mock ID card preview</p>
                            </div>

                            <!-- Right: Detailed Info -->
                            <div class="md:col-span-2 space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Validity Period</h4>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($app->valid_from)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($app->valid_until)->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Purpose</h4>
                                        <p class="text-sm font-medium text-gray-900">{{ $app->id_purpose }}</p>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Access Areas</h4>
                                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border border-gray-100">{{ $app->access_areas ?? 'Standard zones only' }}</p>
                                </div>

                                @if($app->special_permissions)
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Special Permissions</h4>
                                    <p class="text-sm text-gray-700">{{ $app->special_permissions }}</p>
                                </div>
                                @endif

                                @if($app->notes)
                                <div class="p-3 bg-blue-50/50 rounded-lg border-l-4 border-blue-200">
                                    <p class="text-xs text-blue-700 italic">"{{ $app->notes }}"</p>
                                </div>
                                @endif
                                
                                <div class="flex items-center space-x-4 pt-2">
                                    @if($app->fingerprint_path)
                                        <span class="flex items-center text-[10px] text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                            <i data-feather="check" class="w-3 h-3 mr-1"></i> Biometrics Recorded
                                        </span>
                                    @endif
                                    <span class="flex items-center text-[10px] text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">
                                        <i data-feather="check" class="w-3 h-3 mr-1"></i> Signature Verified
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Bar -->
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                        <button class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center">
                            <i data-feather="download" class="w-3 h-3 mr-1"></i> Export Request PDF
                        </button>
                        @if($app->status === 'issued')
                            <button class="text-xs font-bold text-red-600 hover:text-red-800 flex items-center">
                                <i data-feather="alert-octagon" class="w-3 h-3 mr-1"></i> Report Lost/Damaged
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-indigo-100 p-4 rounded-full">
                            <i data-feather="credit-card" class="w-12 h-12 text-indigo-600"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No ID Applications Found</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        This employee has not yet applied for any personnel ID cards or access credentials.
                    </p>
                    <a href="{{ route('personnel-id.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Start New Application
                    </a>
                </div>
            @endforelse
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