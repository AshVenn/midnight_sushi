<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContactInquiry;
use Mary\Traits\Toast; 

class ContactInquiries extends Component
{
    use WithPagination, Toast;

    public string $search = ''; // Recherche
    public string $filterStatus = 'all'; // Filtre par statut
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc']; // Tri par défaut

    public array $filterStatusOptions = [
        ['id' => 'all',     'name' => 'Toutes'],
        ['id' => 'open',    'name' => 'Ouvertes'],
        ['id' => 'resolved','name' => 'Résolues'],
    ];

    /**
     * Réinitialise la pagination lorsque la recherche ou le filtre change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Met à jour le tri des colonnes.
     */
    public function sortByColumn(string $column): void
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Marque une demande comme résolue.
     */
    public function markAsResolved($id)
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->update(['status' => 'resolved']);
            $this->success(
                title: 'Succès',
                description: 'La demande a été marquée comme résolue.'
            );
        }
    }

    /**
     * Supprime toutes les demandes.
     */
    public function clearAll()
    {
        ContactInquiry::truncate();
        $this->success(
            title: 'Succès',
            description: 'Toutes les demandes ont été supprimées.'
        );
    }

    /**
     * Récupère les demandes filtrées, triées et paginées.
     */
    public function inquiries()
    {
        return ContactInquiry::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('subject', 'like', "%{$this->search}%")
                      ->orWhere('message', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    /**
     * Définit les en-têtes de la table.
     */
    public function headers(): array
    {
        return [
            ['key' => 'id',          'label' => 'ID',          'sortable' => true],
            ['key' => 'name',        'label' => 'Nom',         'sortable' => false],
            ['key' => 'email',       'label' => 'Email',       'sortable' => false],
            ['key' => 'subject',     'label' => 'Sujet',       'sortable' => false],
            ['key' => 'message',     'label' => 'Message',     'sortable' => false],
            ['key' => 'status',      'label' => 'Statut',      'sortable' => false],
            ['key' => 'created_at',  'label' => 'Date',        'sortable' => true],
            ['key' => 'actions',    'label' => 'Actions',     'sortable' => false],
        ];
    }

    /**
     * Rend la vue du composant Livewire.
     */
    public function render()
    {
        return view('livewire.admin.contact-inquiries', [
            'inquiries' => $this->inquiries(),
            'headers'   => $this->headers(),
            'sortBy'    => $this->sortBy,
            'filterStatusOptions' => $this->filterStatusOptions,
        ]);
    }
}