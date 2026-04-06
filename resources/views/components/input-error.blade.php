@props(['disabled' => false, 'message'])

@if ($message)
<p {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400']) }}>
    {{ $message }}
</p>
@endif
