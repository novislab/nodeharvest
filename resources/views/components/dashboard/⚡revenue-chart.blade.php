<?php

use Livewire\Component;

new class extends Component
{
    public array $data = [
        ['month' => 'Jan', 'revenue' => 12500],
        ['month' => 'Feb', 'revenue' => 18200],
        ['month' => 'Mar', 'revenue' => 15400],
        ['month' => 'Apr', 'revenue' => 22100],
        ['month' => 'May', 'revenue' => 19800],
        ['month' => 'Jun', 'revenue' => 26500],
        ['month' => 'Jul', 'revenue' => 24100],
    ];

    public function mount(): void
    {
        // Data can be loaded from database here
    }

    public function getMaxRevenue(): int
    {
        return max(array_column($this->data, 'revenue'));
    }
};
?>

<flux:card class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-950">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="lg">Revenue Overview</flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Monthly revenue performance</flux:text>
        </div>
        <div class="text-right">
            <flux:heading size="xl" class="text-2xl font-bold text-zinc-900 dark:text-white">
                ${{ number_format(array_sum(array_column($data, 'revenue'))) }}
            </flux:heading>
            <flux:text size="sm" class="text-emerald-500">+12.5% vs last year</flux:text>
        </div>
    </div>

    <div class="relative h-64">
        <div class="absolute inset-0 flex items-end justify-between gap-2">
            @foreach ($data as $item)
                @php
                    $height = ($item['revenue'] / $this->getMaxRevenue()) * 100;
                    $isHighest = $item['revenue'] === $this->getMaxRevenue();
                @endphp
                <div class="group flex flex-1 flex-col items-center gap-2">
                    <div class="relative w-full">
                        <div
                            class="w-full rounded-t-lg transition-all duration-500 hover:opacity-80 {{ $isHighest ? 'bg-indigo-500' : 'bg-zinc-300 dark:bg-zinc-700' }}"
                            @style(['height: ' . $height . '%'])
                        ></div>
                        <div class="absolute -top-8 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded bg-zinc-800 px-2 py-1 text-xs text-white group-hover:block dark:bg-zinc-700">
                            ${{ number_format($item['revenue']) }}
                        </div>
                    </div>
                    <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">{{ $item['month'] }}</flux:text>
                </div>
            @endforeach
        </div>

        <div class="absolute inset-0 pointer-events-none">
            @for ($i = 0; $i <= 4; $i++)
                <div class="absolute w-full border-t border-zinc-200 dark:border-zinc-800" style="top: {{ $i * 25 }}%"></div>
            @endfor
        </div>
    </div>
</flux:card>
