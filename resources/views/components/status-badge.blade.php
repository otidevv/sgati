@props(['status'])

@php
$colorClasses = match($status) {
    'active' => 'bg-green-100 text-green-800',
    'development' => 'bg-blue-100 text-blue-800',
    'maintenance' => 'bg-yellow-100 text-yellow-800',
    'inactive' => 'bg-red-100 text-red-800',
    default => 'bg-gray-100 text-gray-800',
};

$label = match($status) {
    'active' => 'Activo',
    'development' => 'En Desarrollo',
    'maintenance' => 'En Mantenimiento',
    'inactive' => 'Inactivo',
    default => ucfirst($status),
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClasses}"]) }}>
    {{ $label }}
</span>
