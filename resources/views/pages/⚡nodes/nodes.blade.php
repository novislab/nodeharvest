<div class="space-y-6">
    <div class="flex items-center justify-between">
        <x-dashboard.page-header :breadcrumbs="[['href' => route('dashboard'), 'label' => 'Dashboard'], ['label' => 'Nodes']]" />
        <flux:button variant="primary" icon="plus">
            Create Node
        </flux:button>
    </div>

    {{-- Toolbar --}}
    <flux:card class="bg-zinc-950 border-zinc-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <flux:checkbox />
                    <span class="text-sm text-zinc-400">Select All</span>
                </div>
            </div>
            <div class="relative">
                <flux:input
                    type="search"
                    placeholder="Filter services..."
                    class="w-64 bg-zinc-950 border-zinc-900"
                    icon="magnifying-glass"
                />
            </div>
        </div>
    </flux:card>

    {{-- Services Grid --}}
    <flux:card class="bg-zinc-950 border-zinc-900">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-nodes.node-card
                name="plausible"
                created="Created less than a minute ago"
                
                status-position="right"
                status-color="green-500"
            />

            <x-nodes.node-card
                name="supabase"
                created="Created less than a minute ago"
                status-position="left"
            />

            <x-nodes.node-card
                name="appwrite"
                created="Created 1 minute ago"
                status-position="left"
            />

            <x-nodes.node-card
                name="ghost"
                created="Created about 3 hours ago"
                
                status-position="right"
                status-color="green-500"
            />

            <x-nodes.node-card
                name="mongo"
                created="Created about 3 hours ago"
                icon="mongo"
                
                status-position="right"
                status-color="green-500"
            />

            <x-nodes.node-card
                name="odoo"
                created="Created 2 days ago"
                status-position="right"
                status-color="green-500"
            />

            <x-nodes.node-card
                name="pg"
                created="Created 4 days ago"
                icon="pg"
                status-position="right"
                status-color="green-500"
            />

            <x-nodes.node-card
                name="invoiceninja"
                created="Created about 1 month ago"
                status-position="left"
            />
        </div>
    </flux:card>
</div>