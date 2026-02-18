@props([
    'name',
    'created',
    'icon' => 'window',
    'statusPosition' => null, // 'left', 'right', or null
    'statusColor' => 'zinc-700',
    'selected' => false,
])

@php
$cardBgClass = $selected ? 'bg-zinc-800 border-zinc-700' : 'bg-zinc-950 border-zinc-800';
$statusPositionClass = $statusPosition === 'left' ? '-left-1.5' : '-right-1.5';
@endphp

<div class="relative group">
    <flux:card class="{{ $cardBgClass }} dark:bg-zinc-900/20 hover:border-zinc-700 transition-colors">
        <div class="flex items-start justify-between">
            <flux:checkbox
                :label="$name"
                :description="$created"
            />
            @if($icon === 'mongo')
                <svg class="size-5 text-green-500 mt-1" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            @elseif($icon === 'pg')
                <svg class="size-5 text-blue-400 mt-1" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                </svg>
            @else
                <flux:icon name="{{ $icon }}" class="size-5 text-zinc-500 mt-1" />
            @endif
        </div>
    </flux:card>
    @if($statusPosition)
        <div class="absolute {{ $statusPositionClass }} top-1/2 -translate-y-1/2 w-3 h-3 bg-{{ $statusColor }} rounded-full border-2 border-zinc-950"></div>
    @endif
</div>
