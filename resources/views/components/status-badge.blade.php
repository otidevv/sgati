@props(['status'])

@php
$val = $status instanceof \BackedEnum ? $status->value : (string) $status;

$dot = match($val) {
    'active'      => 'bg-green-500',
    'development' => 'bg-blue-500',
    'maintenance' => 'bg-yellow-400',
    'inactive'    => 'bg-red-400',
    default       => 'bg-gray-400',
};

$colorClasses = match($val) {
    'active'      => 'bg-green-50 text-green-700 ring-green-600/20',
    'development' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
    'maintenance' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
    'inactive'    => 'bg-red-50 text-red-600 ring-red-500/20',
    default       => 'bg-gray-100 text-gray-600 ring-gray-500/20',
};

$label = match($val) {
    'active'      => 'Activo',
    'development' => 'En desarrollo',
    'maintenance' => 'Mantenimiento',
    'inactive'    => 'Inactivo',
    default       => ucfirst($val),
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium ring-1 ring-inset {$colorClasses}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
    {{ $label }}
</span>
