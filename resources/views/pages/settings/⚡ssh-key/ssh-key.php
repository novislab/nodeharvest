<?php

use App\Livewire\Forms\SshKey as SshKeyForm;
use App\Models\SshKey;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('SSH Key')] class extends Component
{
    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Locked]
    public ?int $editingId = null;

    public bool $modalOpen = false;

    #[Locked]
    public ?int $deleteId = null;

    public ?string $deleteName = null;

    public string $confirmName = '';

    public bool $deleteModalOpen = false;

    public array $selected = [];

    public bool $selectAll = false;

    public SshKeyForm $form;

    #[Computed]
    public function sshKeys(): Collection
    {
        return SshKey::when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->get();
    }

    #[Computed]
    public function deletingSshKey(): ?SshKey
    {
        return SshKey::find($this->deleteId);
    }

    public function updatedSearch(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value ? $this->sshKeys->pluck('id')->map(fn ($id) => (string) $id)->toArray() : [];
    }

    public function updatedSelected(): void
    {
        $this->selectAll = count($this->selected) === $this->sshKeys->count() && $this->sshKeys->count() > 0;
    }

    public function openModal(?int $id = null): void
    {
        $this->form->reset();
        $this->editingId = $id;
        $id && $this->form->fill(SshKey::findOrFail($id));
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $this->form->validateKey($this);

        if ($this->getErrorBag()->has('form.key')) {
            return;
        }

        $this->form->validate();

        $ssh = $this->editingId ? SshKey::find($this->editingId) : new SshKey;
        $ssh?->fill($this->form->all())->save();

        Flux::toast(
            text: $this->editingId ? 'Your SSH key has been updated successfully.' : 'Your new SSH key has been added.',
            variant: 'success'
        );

        $this->form->reset();
        $this->editingId = null;
        $this->modalOpen = false;
    }

    public function confirmDelete(int $id): void
    {
        $sshKey = SshKey::find($id);
        $this->deleteId = $id;
        $this->deleteName = $sshKey?->name;
        $this->confirmName = '';
        $this->deleteModalOpen = true;
    }

    public function delete(): void
    {
        if ($this->deletingSshKey->name !== $this->confirmName) {
            $this->addError('confirmName', 'Name does not match');

            return;
        }

        $this->deletingSshKey->delete();
        Flux::toast(text: 'The SSH key has been removed.', variant: 'success');
        $this->reset(['deleteId', 'deleteName', 'confirmName', 'deleteModalOpen']);
    }

    public function cancelDelete(): void
    {
        $this->reset(['deleteId', 'deleteName', 'confirmName', 'deleteModalOpen']);
    }

    public function bulkDelete(): void
    {
        if ($this->selected === []) {
            return;
        }

        $count = SshKey::whereIn('id', $this->selected)->delete();
        Flux::toast(text: "{$count} SSH key(s) have been removed.", variant: 'success');
        $this->selected = [];
        $this->selectAll = false;
    }
};
