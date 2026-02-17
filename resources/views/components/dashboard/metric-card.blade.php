@props([
    'label',
    'value',
    'change',
    'changeType' => 'positive', // 'positive' or 'negative'
])

@php
$bgClass = $changeType === 'positive' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400';
@endphp

<flux:card class="flex min-h-28 flex-col justify-center gap-3 rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-950">
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $label }}</flux:text>
    <div class="flex items-end justify-between">
        <flux:heading size="lg" class="text-[26px] font-bold text-zinc-900 dark:text-white">{{ $value }}</flux:heading>
        <div class="flex items-center gap-1">
            <span class="inline-flex items-center rounded {{ $bgClass }} px-2 py-0.5 text-xs font-medium">{{ $change }}</span>
            <flux:text size="xs" class="text-zinc-400 dark:text-zinc-500">Vs last month</flux:text>
        </div>
    </div>
</flux:card>
