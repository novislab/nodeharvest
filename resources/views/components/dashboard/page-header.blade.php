@props([
    'breadcrumbs' => [],
])

<flux:breadcrumbs>
    @foreach ($breadcrumbs as $crumb)
        <flux:breadcrumbs.item
            href="{{ $crumb['href'] ?? '#' }}"
            wire:navigate
            separator="slash"
            :active="$loop->last"
        >
            {{ $crumb['label'] }}
        </flux:breadcrumbs.item>
    @endforeach
</flux:breadcrumbs>
