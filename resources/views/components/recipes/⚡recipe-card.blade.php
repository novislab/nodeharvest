<?php

use App\Models\Recipe;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $recipeId;

    public array $locations = [];

    public Recipe $recipe;

    public function mount(int $recipeId, array $locations): void
    {
        $this->recipeId = $recipeId;
        $this->locations = $locations;
        $this->recipe = Recipe::findOrFail($recipeId);
    }

    public function delete(): void
    {
        $this->recipe->delete();
        Flux::toast(text: 'Recipe deleted successfully.', variant: 'success');
        $this->dispatch('recipe-saved');
    }
};
?>

@php
$locationName = $locations[$recipe->location] ?? $recipe->location;
$modalName = "delete-recipe-{$recipe->id}";
@endphp

<div class="relative group" x-data>
    <flux:card class="bg-zinc-950 border-zinc-800 dark:bg-zinc-900/20 hover:border-zinc-700 transition-colors">
        <div class="space-y-4">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-2">
                <div 
                    class="flex-1 min-w-0 cursor-pointer" 
                    @click="$dispatch('open-recipe-modal', { recipeId: {{ $recipe->id }} })"
                >
                    <flux:heading size="md" class="truncate">{{ $recipe->name }}</flux:heading>
                </div>
                
                <flux:modal.trigger :name="$modalName">
                    <flux:button variant="danger" size="xs" icon="trash" />
                </flux:modal.trigger>
            </div>
            
            {{-- Specs Grid --}}
            <div 
                class="grid grid-cols-2 gap-3 text-sm cursor-pointer" 
                @click="$dispatch('open-recipe-modal', { recipeId: {{ $recipe->id }} })"
            >
                <div class="flex items-center gap-2">
                    <flux:icon name="map-pin" class="size-4 text-zinc-500" />
                    <span class="text-zinc-400">{{ $locationName }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <flux:icon name="server" class="size-4 text-zinc-500" />
                    <span class="text-zinc-400">{{ $recipe->preset_id }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <flux:icon name="computer-desktop" class="size-4 text-zinc-500" />
                    <span class="text-zinc-400">OS: {{ $recipe->os_id }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <flux:icon name="credit-card" class="size-4 text-zinc-500" />
                    <span class="text-zinc-400">{{ ucfirst(str_replace('-', ' ', $recipe->deploy_period)) }}</span>
                </div>
            </div>
            
            {{-- Footer --}}
            <div 
                class="pt-3 border-t border-zinc-800 cursor-pointer" 
                @click="$dispatch('open-recipe-modal', { recipeId: {{ $recipe->id }} })"
            >
                <div class="flex items-center justify-between text-xs text-zinc-500">
                    <span>{{ $recipe->created_at->diffForHumans() }}</span>
                    @if($recipe->software_id)
                        <flux:badge size="sm" color="zinc">+ Software</flux:badge>
                    @endif
                </div>
            </div>
        </div>
    </flux:card>
    
    {{-- Delete Modal --}}
    <flux:modal :name="$modalName" class="min-w-[22rem] bg-zinc-950 border border-zinc-800">
        <div class="space-y-6 p-6">
            <div>
                <flux:heading size="lg">Delete recipe?</flux:heading>
                <flux:text class="mt-2 text-zinc-400">
                    You're about to delete this recipe.<br>
                    This action cannot be reversed.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">Delete recipe</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
