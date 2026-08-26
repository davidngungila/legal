@props([
    'name' => 'signature',
    'label' => 'Signature',
    'required' => false,
    'existingPath' => '',
    'canvasWidth' => 400,
    'canvasHeight' => 150,
])

<div class="mb-4" id="{{ $name }}_container">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div data-signature-pad
         data-name="{{ $name }}"
         data-required="{{ $required ? 'true' : 'false' }}"
         data-existing-path="{{ $existingPath }}"
         data-canvas-width="{{ $canvasWidth }}"
         data-canvas-height="{{ $canvasHeight }}">
    </div>
</div>
