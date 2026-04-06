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
    'active'      => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-500/30',
    'development' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ring-blue-600/20 dark:ring-blue-500/30',
    'maintenance' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-500/30',
    'inactive'    => 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 ring-red-500/20 dark:ring-red-500/30',
    default       => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 ring-gray-500/20 dark:ring-gray-500/30',
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
