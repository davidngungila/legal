@props([
    'id' => 'modal',
    'title' => 'Modal',
    'description' => '',
    'icon' => 'info',
    'color' => 'indigo', // indigo, purple, blue, green, red, orange, yellow, pink
    'size' => 'md', // sm, md, lg, xl
    'showClose' => true,
    'backdropClose' => true,
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
    
    $iconColors = [
        'indigo' => 'text-indigo-600',
        'purple' => 'text-purple-600',
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'red' => 'text-red-600',
        'orange' => 'text-orange-600',
        'yellow' => 'text-yellow-600',
        'pink' => 'text-pink-600',
    ];
    
    $gradient = $colorGradients[$color] ?? $colorGradients['indigo'];
    $bgColor = $colorClasses[$color] ?? $colorClasses['indigo'];
    $iconColor = $iconColors[$color] ?? $iconColors['indigo'];
    
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    @if($backdropClose)
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeModal('{{ $id }}')"></div>
    @else
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
    @endif
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full {{ $sizeClass }} transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r {{ $gradient }} rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 {{ $bgColor }} rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="{{ $icon }}" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>
                            @if($description)
                                <p class="text-sm text-gray-500">{{ $description }}</p>
                            @endif
                        </div>
                    </div>
                    @if($showClose)
                        <button type="button" onclick="closeModal('{{ $id }}')" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                            <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
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

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)');
        openModals.forEach(modal => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    }
});
</script>
