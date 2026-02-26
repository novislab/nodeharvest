<div class="space-y-6">
    <div class="flex items-center justify-between">
        <x-dashboard.page-header :breadcrumbs="[['label' => 'SSH Key']]" />
        <flux:button variant="primary" icon="plus" wire:click="openModal()">
            Add SSH Key
        </flux:button>
    </div>

    <flux:card class="bg-zinc-950 border-zinc-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model="selectAll" />
                    <span class="text-sm text-zinc-400">Select All</span>
                    @if(count($selected) > 0)
                        <flux:button size="xs" wire:click="bulkDelete()" variant="danger" class="ml-2">
                            Delete ({{ count($selected) }})
                        </flux:button>
                    @endif
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

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Key</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($sshKeys as $ssh)
                    <flux:table.row>
                        <flux:table.cell>
                            <flux:checkbox value="{{ $ssh->id }}" wire:model="selected" />
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $ssh->name }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ Str::limit($ssh->key, 40) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" wire:click="openModal({{ $ssh->id }})" variant="primary">Edit</flux:button>
                            <flux:button size="xs" wire:click="confirmDelete({{ $ssh->id }})" variant="danger">Delete</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-gray-500 py-8">No SSH keys found</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal name="ssh-key-modal" wire:model="modalOpen" class="md:w-[500px]">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit' : 'Create' }} SSH Key</flux:heading>
                <flux:text class="text-sm text-zinc-500 mt-1">Manage your SSH keys for server access</flux:text>
            </div>
            
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" placeholder="e.g., Production Server" class="w-full" />
                @error('name')<flux:error>{{ $message }}</flux:error>@enderror
            </flux:field>
            
            <flux:field>
                <flux:label>SSH Key</flux:label>
                <flux:textarea wire:model="key" rows="8" placeholder="ssh-rsa AAAA..." class="w-full font-mono text-sm" />
                @error('key')<flux:error>{{ $message }}</flux:error>@enderror
            </flux:field>
            
            <div class="flex gap-3 justify-end pt-2">
                <flux:button type="button" wire:click="$set('modalOpen', false)" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingId ? 'Update' : 'Create' }} SSH Key
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-ssh-key-modal" wire:model="deleteModalOpen" class="md:w-[450px]">
        <form wire:submit.prevent="delete" class="space-y-6">
            <div class="text-red-500">
                <flux:heading size="lg">Delete SSH Key</flux:heading>
                <flux:text class="text-sm text-zinc-500 mt-1">This action cannot be undone. Please type the SSH key name to confirm deletion.</flux:text>
            </div>

            <flux:field>
                <flux:label>Type "{{ $sshKeys->find($deleteId)?->name }}" to confirm</flux:label>
                <flux:input wire:model="confirmName" placeholder="Enter SSH key name" class="w-full" />
                @error('confirmName')<flux:error>{{ $message }}</flux:error>@enderror
            </flux:field>

            <div class="flex gap-3 justify-end pt-2">
                <flux:button type="button" wire:click="cancelDelete()" variant="ghost">Cancel</flux:button>
                <flux:button type="submit" variant="danger">Delete SSH Key</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:toast />
</div>
