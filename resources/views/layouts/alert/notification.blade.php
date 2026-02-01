@props(['type' => 'success', 'message' => null])

@php
    $classes = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-orange-100 border-orange-400 text-orange-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700',
    ];

    $icons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'warning' => 'fa-triangle-exclamation',
        'info' => 'fa-circle-info',
    ];

    $style = $classes[$type] ?? $classes['info'];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => "border px-4 py-3 rounded relative mb-4 flex items-center gap-3 $style"]) }}
        role="alert">
        <i class="fa-solid {{ $icon }}"></i>
        <span class="block sm:inline">{{ $message }}</span>
    </div>
@endif
