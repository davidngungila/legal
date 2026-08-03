@props([
    'id' => 'modal',
    'title' => 'Modal',
    'description' => '',
    'icon' => 'info',
    'color' => 'indigo', // indigo, purple, blue, green, red, orange, yellow, pink
    'size' => 'md', // sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, full
    'showClose' => true,
    'backdropClose' => true,
    'zIndex' => 50,
    'titleId' => null,
    'subtitleId' => null,
])

@php
    $colorGradients = [
        'indigo' => 'from-indigo-50 to-purple-50',
        'purple' => 'from-purple-50 to-pink-50',
        'blue' => 'from-blue-50 to-cyan-50',
        'green' => 'from-green-50 to-emerald-50',
        'red' => 'from-red-50 to-orange-50',
        'orange' => 'from-orange-50 to-amber-50',
        'yellow' => 'from-yellow-50 to-amber-50',
        'pink' => 'from-pink-50 to-rose-50',
    ];

    $colorClasses = [
        'indigo' => 'bg-indigo-600',
        'purple' => 'bg-purple-600',
        'blue' => 'bg-blue-600',
        'green' => 'bg-green-600',
        'red' => 'bg-red-600',
        'orange' => 'bg-orange-600',
        'yellow' => 'bg-yellow-600',
        'pink' => 'bg-pink-600',
    ];

    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full',
    ];

    $gradient = $colorGradients[$color] ?? $colorGradients['indigo'];
    $bgColor = $colorClasses[$color] ?? $colorClasses['indigo'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div id="{{ $id }}" data-modal-root class="modal-root hidden" style="z-index: {{ $zIndex }}">
    <!-- Backdrop with blur -->
    <div class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
        @if($backdropClose) onclick="closeModal('{{ $id }}')" @endif></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 overflow-y-auto">
        <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
            <div class="modal-panel relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full {{ $sizeClass }} max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r {{ $gradient }} rounded-t-2xl flex-shrink-0">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-10 h-10 {{ $bgColor }} rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i data-feather="{{ $icon }}" class="w-5 h-5 text-white"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate" @if($titleId) id="{{ $titleId }}" @endif>{{ $title }}</h3>
                                @if($description)
                                    <p class="text-sm text-gray-500 truncate" @if($subtitleId) id="{{ $subtitleId }}" @endif>{{ $description }}</p>
                                @endif
                            </div>
                        </div>
                        @if($showClose)
                            <button type="button" onclick="closeModal('{{ $id }}')"
                                class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center flex-shrink-0">
                                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-6 overflow-y-auto flex-1">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                @if(isset($footer))
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex-shrink-0">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
