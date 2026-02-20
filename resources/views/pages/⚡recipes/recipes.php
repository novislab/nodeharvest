<?php

use App\Models\Recipe;
use App\Services\HostkeyApiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app')] #[Title('Recipes')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public array $locations = [];

    protected HostkeyApiService $apiService;

    public function boot(HostkeyApiService $apiService): void
    {
        $this->apiService = $apiService;
        $this->locations = $apiService->getLocations();
    }

    #[Computed]
    public function recipes()
    {
        return Recipe::query()
            ->where('user_id', Auth::id())
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function createRecipe(): void
    {
        $this->dispatch('open-recipe-modal');
    }

    #[On('recipe-saved')]
    public function refreshRecipes(): void
    {
        unset($this->recipes);
    }
};
