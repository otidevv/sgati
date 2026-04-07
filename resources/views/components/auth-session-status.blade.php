@props(['status'])

@if ($status)
<div {{ $attributes->merge(['class' => 'mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg']) }}>
    <p class="text-sm text-green-700 dark:text-green-300">{{ $status }}</p>
</div>
@endif
