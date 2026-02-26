<?php

use App\Models\SshKey;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('SSH Key')] class extends Component
{
    #[Url(as: 'search', except: '')]
    public string $search = '';

    public string $name = '';

    public string $key = '';

    public ?int $editingId = null;

    public bool $modalOpen = false;

    public ?int $deleteId = null;

    public string $confirmName = '';

    public bool $deleteModalOpen = false;

    public array $selected = [];

    public bool $selectAll = false;

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'key' => ['required', 'string'],
    ];

    public function updatedSearch(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = $this->sshKeys->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected(): void
    {
        $this->selectAll = count($this->selected) === $this->sshKeys->count() && $this->sshKeys->count() > 0;
    }

    public function openModal(?int $id = null): void
    {
        $this->reset(['name', 'key', 'editingId']);
        if ($id) {
            $ssh = SshKey::find($id);
            if ($ssh) {
                $this->editingId = $ssh->id;
                $this->name = $ssh->name;
                $this->key = $ssh->key;
            }
        }
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $this->validate();
        if ($this->editingId) {
            $ssh = SshKey::find($this->editingId);
            $ssh?->update(['name' => $this->name, 'key' => $this->key]);
            Flux::toast(heading: 'SSH key updated', text: 'Your SSH key has been updated successfully.', variant: 'success');
        } else {
            SshKey::create(['name' => $this->name, 'key' => $this->key]);
            Flux::toast(heading: 'SSH key created', text: 'Your new SSH key has been added.', variant: 'success');
        }
        $this->reset(['name', 'key', 'editingId', 'modalOpen']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmName = '';
        $this->deleteModalOpen = true;
    }

    public function delete(): void
    {
        $ssh = SshKey::find($this->deleteId);
        if ($ssh && $ssh->name === $this->confirmName) {
            $ssh->delete();
            Flux::toast(heading: 'SSH key deleted', text: 'The SSH key has been removed.', variant: 'success');
        } else {
            $this->addError('confirmName', 'Name does not match');

            return;
        }
        $this->reset(['deleteId', 'confirmName', 'deleteModalOpen']);
    }

    public function cancelDelete(): void
    {
        $this->reset(['deleteId', 'confirmName', 'deleteModalOpen']);
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }
        $count = SshKey::whereIn('id', $this->selected)->delete();
        Flux::toast(heading: 'SSH keys deleted', text: "{$count} SSH key(s) have been removed.", variant: 'success');
        $this->selected = [];
        $this->selectAll = false;
    }

    public function render(): \Illuminate\View\View
    {
        $sshKeys = SshKey::when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->get();

        return view('pages.settings.⚡ssh-key.ssh-key', [
            'sshKeys' => $sshKeys,
        ]);
    }
};
