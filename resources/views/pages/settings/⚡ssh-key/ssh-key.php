<?php

use App\Livewire\Forms\SshKey as SshKeyForm;
use App\Models\SshKey;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
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
        $query = SshKey::query();

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return $query->latest()->get();
    }

    #[Computed]
    public function deletingSshKey(): ?SshKey
    {
        return SshKey::query()->find($this->deleteId);
    }

    public function updatedSearch(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        if (! $value) {
            $this->selected = [];

            return;
        }

        $this->selected = $this->sshKeys->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function updatedSelected(): void
    {
        $totalKeys = $this->sshKeys->count();

        $this->selectAll = $totalKeys > 0 && count($this->selected) === $totalKeys;
    }

    public function openModal(?int $id = null): void
    {
        $this->form->reset();
        $this->editingId = $id;

        if ($id !== null) {
            $sshKey = SshKey::query()->findOrFail($id);
            $this->form->fill($sshKey);
        }

        $this->modalOpen = true;
    }

    public function save(): void
    {
        if (! $this->validateForm()) {
            return;
        }

        $sshKey = $this->editingId
            ? SshKey::query()->findOrFail($this->editingId)
            : new SshKey;

        $sshKey->fill($this->form->all());
        $sshKey->save();

        Flux::toast(
            text: $this->editingId ? 'Your SSH key has been updated successfully.' : 'Your new SSH key has been added.',
            variant: 'success'
        );

        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $sshKey = SshKey::query()->findOrFail($id);

        $this->deleteId = $id;
        $this->deleteName = $sshKey->name;
        $this->confirmName = '';
        $this->deleteModalOpen = true;
    }

    public function delete(): void
    {
        if ($this->deletingSshKey->name !== $this->confirmName) {
            Flux::toast(text: 'Name does not match', variant: 'danger');

            return;
        }

        $this->deletingSshKey->delete();

        Flux::toast(text: 'The SSH key has been removed.', variant: 'success');

        $this->resetDeleteState();
    }

    public function cancelDelete(): void
    {
        $this->resetDeleteState();
    }

    public function bulkDelete(): void
    {
        if ($this->selected === []) {
            return;
        }

        $deletedCount = SshKey::query()->whereKey($this->selected)->delete();

        Flux::toast(
            text: "{$deletedCount} SSH key(s) have been removed.",
            variant: 'success'
        );

        $this->selected = [];
        $this->selectAll = false;
    }

    private function validateForm(): bool
    {
        try {
            $this->form->validate();
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $error) {
                Flux::toast(text: $error, variant: 'danger');
            }

            return false;
        }

        return true;
    }

    private function resetForm(): void
    {
        $this->form->reset();
        $this->editingId = null;
        $this->modalOpen = false;
    }

    private function resetDeleteState(): void
    {
        $this->reset(['deleteId', 'deleteName', 'confirmName', 'deleteModalOpen']);
    }
};
