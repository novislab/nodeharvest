<div class="space-y-6">
    <div class="flex items-center justify-between">
        <x-dashboard.page-header :breadcrumbs="[['label' => 'SSH Key']]" />
        <flux:button variant="primary" icon="plus" wire:click="openModal()">
            Add SSH Key
        </flux:button>
    </div>

    <flux:card class="bg-zinc-950 border-zinc-900">
        <div class="flex items-center justify-between py-3">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model.live="selectAll" />
                    <span class="text-sm text-zinc-400">Select All</span>
                    <flux:button 
                        size="sm" 
                        wire:click="bulkDelete()" 
                        variant="danger" 
                        class="ml-2"
                        :disabled="empty($selected)"
                    >
                        Delete ({{ count($selected) }})
                    </flux:button>
                </div>
            </div>
            <div class="relative">
                <flux:input
                    type="search"
                    placeholder="Search SSH keys..."
                    class="w-64 bg-zinc-950 border-zinc-900"
                    icon="magnifying-glass"
                    wire:model="search"
                />
            </div>
        </div>
    </flux:card>

    <flux:card class="bg-zinc-950 border-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Key</flux:table.column>
                <flux:table.column>Default</flux:table.column>
                <flux:table.column>Created</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($this->sshKeys as $ssh)
                    <flux:table.row wire:key="{{ $ssh->id }}">
                        <flux:table.cell>
                            <flux:checkbox value="{{ $ssh->id }}" wire:model.live="selected" />
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $ssh->name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ Str::limit($ssh->key, 40) }}</flux:table.cell>
                        <flux:table.cell>
                            @if($ssh->is_default)
                                <flux:badge size="sm" color="emerald">Default</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-400">{{ $ssh->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell class="flex gap-2">
                            <flux:button size="sm" wire:click="openModal({{ $ssh->id }})" variant="primary" icon:trailing="file-pen-line">Edit</flux:button>
                            <flux:button size="sm" wire:click="confirmDelete({{ $ssh->id }})" variant="danger" icon:trailing="trash">Delete</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-gray-500 py-8">No SSH keys found</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal name="ssh-key-modal" wire:model="modalOpen" class="md:w-125">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit' : 'Create' }} SSH Key</flux:heading>
            </div>
            
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="form.name" class="w-full" required />
            </flux:field>

            <flux:field>
                <flux:label>SSH Key</flux:label>
                <flux:textarea wire:model="form.key" rows="2" placeholder="ssh-rsa AAAA..." class="w-full font-mono text-sm" required />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="form.is_default" label="Set as default SSH key" />
            </flux:field>
            
            <div class="flex gap-3 justify-end pt-2">
                <flux:button type="button" wire:click="$set('modalOpen', false)" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingId ? 'Update' : 'Create' }} SSH Key
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal 
        name="delete-ssh-key-modal" 
        wire:model="deleteModalOpen" 
        class="md:w-112.5"
    >
        <form wire:submit.prevent="delete" class="space-y-6">
            <div class="text-red-500">
                <flux:heading size="lg">Delete SSH Key</flux:heading>
                <flux:text class="text-sm text-zinc-500 mt-1">This action cannot be undone. Please type the SSH key name to confirm deletion.</flux:text>
            </div>

            <flux:field>
                <div class="flex items-center gap-2" x-data="{ copied: false }">
                    <flux:label>Type "</flux:label>
                    <span class="font-mono text-(--color-accent) underline font-semibold">{{ $this->deleteName }}</span>
                    <flux:button type="button" size="xs" variant="ghost" class="p-1!" @click="navigator.clipboard.writeText('{{ $this->deleteName }}'); copied = true; setTimeout(() => copied = false, 2000)">
                        <flux:icon x-show="!copied" name="copy" class="w-4 h-4" />
                        <flux:icon x-cloak x-show="copied" name="check" class="w-4 h-4 text-emerald-500" />
                    </flux:button>
                    <flux:label>" to confirm</flux:label>
                </div>
                <flux:input wire:model="confirmName" placeholder="Enter SSH key name" class="w-full" />
            </flux:field>

            <div class="flex gap-3 justify-end pt-2">
                <flux:button type="button" wire:click="cancelDelete()" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="danger">Delete SSH Key</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
