@props(['type' => 'info', 'title' => null, 'message', 'dismissible' => true])

@php
$alertStyles = [
    'info' => 'bg-blue-50 dark:bg-blue-900/30 border-blue-400 dark:border-blue-700 text-blue-800 dark:text-blue-200',
    'success' => 'bg-green-50 dark:bg-green-900/30 border-green-400 dark:border-green-700 text-green-800 dark:text-green-200',
    'warning' => 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-400 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200',
    'error' => 'bg-red-50 dark:bg-red-900/30 border-red-400 dark:border-red-700 text-red-800 dark:text-red-200',
];
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-4 rounded-lg {$alertStyles[$type]}"]) }} x-data="{ show: true }" x-show="show">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'info')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif($type === 'success')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif($type === 'warning')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            @elseif($type === 'error')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div class="ml-3 flex-1">
            @if($title)
            <p class="text-sm font-medium">{{ $title }}</p>
            @endif
            <p class="text-sm">{{ $message }}</p>
        </div>
        @if($dismissible)
        <button @click="show = false" class="ml-3 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        @endif
    </div>
</div>
