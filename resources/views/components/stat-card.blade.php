@props(['title', 'value', 'icon', 'color' => 'blue', 'href' => null, 'subtitle' => null])

@php
$colorVariants = [
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'yellow' => 'bg-yellow-500',
    'red' => 'bg-red-500',
    'purple' => 'bg-purple-500',
    'indigo' => 'bg-indigo-500',
];

$bgColor = $colorVariants[$color] ?? $colorVariants['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300']) }}>
    @if($href)
    <a href="{{ $href }}" class="block">
    @endif
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="{{ $bgColor }} rounded-md p-3">
                        {!! $icon !!}
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ $title }}
                        </dt>
                        <dd>
                            <div class="text-2xl font-bold text-gray-900">
                                {{ $value }}
                            </div>
                        </dd>
                    </dl>
                    @if($subtitle)
                    <div class="mt-1">
                        <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @if($href)
    </a>
    @endif
</div>
