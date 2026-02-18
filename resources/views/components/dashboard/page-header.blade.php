@props([
    'breadcrumbs' => [],
])

<flux:breadcrumbs>
    @foreach ($breadcrumbs as $crumb)
        @if (!empty($crumb['href']))
            <flux:breadcrumbs.item
                href="{{ $crumb['href'] }}"
                wire:navigate.hover
                separator="slash"
                :active="$loop->last"
            >
                {{ $crumb['label'] }}
            </flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item
                separator="slash"
                :active="$loop->last"
            >
                {{ $crumb['label'] }}
            </flux:breadcrumbs.item>
        @endif
    @endforeach
</flux:breadcrumbs>
