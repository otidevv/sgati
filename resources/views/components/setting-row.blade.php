@props(['label', 'value' => null])

<div class="flex flex-col gap-1">
    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $label }}</span>
    @if($slot->isNotEmpty())
        <div>{{ $slot }}</div>
    @else
        <span class="text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $value ?? '—' }}</span>
    @endif
</div>
