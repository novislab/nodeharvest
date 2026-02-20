<?php

use App\Models\Recipe;
use App\Services\HostkeyApiService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public ?int $recipeId = null;

    public string $name = '';

    public string $preset_id = '';

    public string $location = 'NL';

    public int $os_id = 0;

    public ?int $software_id = null;

    public int $traffic_plan_id = 0;

    public string $deploy_period = 'monthly';

    public string $ssh_key = '';

    public string $post_install_script = '';

    public string $post_install_callback = '';

    public array $presets = [];

    public array $operatingSystems = [];

    public array $software = [];

    public array $trafficPlans = [];

    public array $locations = [];

    public bool $loaded = false;

    public string $tab = 'basic';

    protected HostkeyApiService $apiService;

    public function boot(HostkeyApiService $apiService): void
    {
        $this->apiService = $apiService;
        $this->locations = $apiService->getLocations();
    }

    #[On('load-recipe-data')]
    public function loadData(?int $recipeId = null): void
    {
        $this->recipeId = $recipeId;
        $this->loaded = false;

        // Reset form
        $this->reset([
            'name', 'preset_id', 'os_id', 'software_id',
            'traffic_plan_id', 'ssh_key', 'post_install_script',
            'post_install_callback', 'presets', 'operatingSystems',
            'software', 'trafficPlans',
        ]);
        $this->location = 'NL';
        $this->tab = 'basic';
        $this->resetErrorBag();

        // Load data
        if ($recipeId) {
            $recipe = Recipe::findOrFail($recipeId);
            $this->name = $recipe->name;
            $this->preset_id = $recipe->preset_id;
            $this->location = $recipe->location;
            $this->os_id = $recipe->os_id;
            $this->software_id = $recipe->software_id;
            $this->traffic_plan_id = $recipe->traffic_plan_id;
            $this->deploy_period = $recipe->deploy_period;
            $this->ssh_key = $recipe->ssh_key ?? '';
            $this->post_install_script = $recipe->post_install_script ?? '';
            $this->post_install_callback = $recipe->post_install_callback ?? '';
        }

        // Load presets
        $this->presets = $this->apiService->getPresets($this->location);

        // Load OS/software if preset selected
        if ($this->preset_id !== '' && $this->preset_id !== '0') {
            $this->operatingSystems = $this->apiService->getOperatingSystems((int) $this->preset_id);
            $this->software = $this->apiService->getSoftware($this->location, (int) $this->preset_id);
            $this->trafficPlans = $this->apiService->getTrafficPlans($this->location, (int) $this->preset_id);
        }

        $this->loaded = true;
    }

    public function updatedLocation(string $value): void
    {
        $this->preset_id = '';
        $this->os_id = 0;
        $this->software_id = null;
        $this->traffic_plan_id = 0;
        $this->operatingSystems = [];
        $this->software = [];
        $this->trafficPlans = [];
        $this->presets = $this->apiService->getPresets($value);
    }

    public function updatedPresetId(string $value): void
    {
        $this->os_id = 0;
        $this->software_id = null;
        $this->traffic_plan_id = 0;
        $this->operatingSystems = [];
        $this->software = [];
        $this->trafficPlans = [];

        if ($value !== '' && $value !== '0') {
            $this->operatingSystems = $this->apiService->getOperatingSystems((int) $value);
            $this->software = $this->apiService->getSoftware($this->location, (int) $value);
            $this->trafficPlans = $this->apiService->getTrafficPlans($this->location, (int) $value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'preset_id' => ['required', 'string'],
            'location' => ['required', 'string', 'size:2', Rule::in(array_keys($this->locations))],
            'os_id' => ['required', 'integer', 'min:1'],
            'software_id' => ['nullable', 'integer'],
            'traffic_plan_id' => ['required', 'integer', 'min:1'],
            'deploy_period' => ['required', Rule::in(['monthly', 'quarterly', 'semi-annually', 'annually'])],
            'ssh_key' => ['nullable', 'string'],
            'post_install_script' => ['nullable', 'string'],
            'post_install_callback' => ['nullable', 'url'],
        ]);

        $data = [
            'name' => $this->name,
            'preset_id' => $this->preset_id,
            'location' => $this->location,
            'os_id' => $this->os_id,
            'software_id' => $this->software_id,
            'traffic_plan_id' => $this->traffic_plan_id,
            'deploy_period' => $this->deploy_period,
            'ssh_key' => $this->ssh_key ?: null,
            'post_install_script' => $this->post_install_script ?: null,
            'post_install_callback' => $this->post_install_callback ?: null,
            'user_id' => Auth::id(),
        ];

        if ($this->recipeId) {
            Recipe::where('id', $this->recipeId)->update($data);
            Flux::toast(text: 'Recipe updated successfully.', variant: 'success');
        } else {
            Recipe::create($data);
            Flux::toast(text: 'Recipe created successfully.', variant: 'success');
        }

        $this->dispatch('recipe-saved');
        $this->dispatch('close-recipe-modal');
    }
};
?>

<div 
    x-data="{ open: false, loading: false }"
    @open-recipe-modal.window="
        open = true;
        loading = true;
        $nextTick(() => $wire.loadData($event.detail.recipeId));
    "
    @close-recipe-modal.window="open = false"
>
    <flux:modal x-model="open" class="max-w-2xl bg-zinc-950 border border-zinc-800">
        <div class="p-6">
            <flux:heading size="lg">{{ $recipeId ? 'Edit' : 'Create' }} Recipe</flux:heading>
            <flux:text class="mt-2 text-zinc-400">Configure a reusable VPS server template.</flux:text>

            @if(!$loaded)
                <div class="mt-6 space-y-6">
                    <flux:skeleton.group animate="shimmer">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <flux:skeleton.line class="h-10" />
                            <flux:skeleton.line class="h-10" />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <flux:skeleton.line class="h-10" />
                            <flux:skeleton.line class="h-10" />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <flux:skeleton.line class="h-10" />
                            <flux:skeleton.line class="h-10" />
                        </div>
                    </flux:skeleton.group>
                </div>
            @else
                <form wire:submit="save" class="mt-6">
                    <flux:tab.group>
                        <flux:tabs wire:model="tab">
                            <flux:tab name="basic">Basic</flux:tab>
                            <flux:tab name="advanced">Advanced</flux:tab>
                        </flux:tabs>

                        <flux:tab.panel name="basic" class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="name" label="Recipe Name" placeholder="My VPS Config" required />

                                <flux:select wire:model.live="location" label="Location" required>
                                    @foreach($locations as $code => $name)
                                        <flux:select.option value="{{ $code }}">{{ $name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <flux:select wire:model.live="preset_id" label="Server Preset" required>
                                        <flux:select.option value="">
                                            @if(empty($presets))
                                                Loading...
                                            @else
                                                Select a preset
                                            @endif
                                        </flux:select.option>
                                        @foreach($presets as $preset)
                                            <flux:select.option value="{{ $preset['id'] }}">
                                                {{ $preset['name'] }} - {{ $preset['description'] ?? '' }}
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <flux:select wire:model="deploy_period" label="Billing Period" required>
                                    <flux:select.option value="monthly">Monthly</flux:select.option>
                                    <flux:select.option value="quarterly">Quarterly</flux:select.option>
                                    <flux:select.option value="semi-annually">Semi-Annually</flux:select.option>
                                    <flux:select.option value="annually">Annually</flux:select.option>
                                </flux:select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <flux:select wire:model="os_id" label="Operating System" required>
                                        <flux:select.option value="0">
                                            @if(empty($operatingSystems))
                                                Select preset first
                                            @else
                                                Select an OS
                                            @endif
                                        </flux:select.option>
                                        @foreach($operatingSystems as $os)
                                            <flux:select.option value="{{ $os['id'] }}">{{ $os['name'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <flux:select wire:model="traffic_plan_id" label="Traffic Plan" required>
                                    <flux:select.option value="0">
                                        @if(empty($trafficPlans))
                                            Select preset first
                                        @else
                                            Select a traffic plan
                                        @endif
                                    </flux:select.option>
                                    @foreach($trafficPlans as $plan)
                                        <flux:select.option value="{{ $plan['id'] }}">{{ $plan['name'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </flux:tab.panel>

                        <flux:tab.panel name="advanced" class="mt-4 space-y-4">
                            <flux:textarea wire:model="ssh_key" label="SSH Public Key (Optional)" placeholder="ssh-rsa AAAAB3..." rows="3" />

                            <flux:textarea wire:model="post_install_script" label="Post-Install Script (Optional)" placeholder="#!/bin/bash..." rows="3" />

                            <div class="grid grid-cols-2 gap-4">
                                <flux:select wire:model="software_id" label="Software (Optional)">
                                    <flux:select.option value="">None</flux:select.option>
                                    @foreach($software as $soft)
                                        <flux:select.option value="{{ $soft['id'] }}">{{ $soft['name'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>

                                <flux:input wire:model="post_install_callback" label="Callback URL (Optional)" placeholder="https://example.com/callback" type="url" />
                            </div>
                        </flux:tab.panel>
                    </flux:tab.group>

                    <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-zinc-800">
                        <flux:button type="button" @click="open = false" variant="ghost">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">{{ $recipeId ? 'Update' : 'Create' }}</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>
