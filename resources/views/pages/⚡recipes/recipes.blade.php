<div class="space-y-6" x-data>
    <div class="flex items-center justify-between">
        <x-dashboard.page-header :breadcrumbs="[['href' => route('dashboard'), 'label' => 'Dashboard'], ['label' => 'Recipes']]" />
        <flux:button 
            variant="primary" 
            @click="$dispatch('open-recipe-modal', { recipeId: null })"
            icon="plus"
        >
            Create Recipe
        </flux:button>
    </div>

    {{-- Recipes Grid --}}
    <flux:card class="bg-zinc-950 border-zinc-900">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->recipes as $recipe)
                <livewire:recipes.recipe-card :key="$recipe->id" :recipeId="$recipe->id" :locations="$locations" />
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <flux:icon name="clipboard-document-list" class="size-12 text-zinc-600 mb-4" />
                    <flux:heading size="lg" class="text-zinc-400">No recipes yet</flux:heading>
                    <flux:text class="text-zinc-500 mt-2">Create your first recipe to get started.</flux:text>
                    <flux:button 
                        variant="primary" 
                        @click="$dispatch('open-recipe-modal', { recipeId: null })"
                        icon="plus" 
                        class="mt-4"
                    >
                        Create Recipe
                    </flux:button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($this->recipes->hasPages())
            <div class="mt-6">
                {{ $this->recipes->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Modal Component --}}
    <livewire:recipes.modal :key="'recipe-modal'" />
</div>
