<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;

class MenuDetailsModal extends Component
{
    public ?Menu $menu = null;
    public bool $isModalOpen = false;

    protected $listeners = [
        'openMenuModal' => 'openModal',
    ];

    public function openModal($menuId)
    {
        $this->menu = Menu::with('products')->find($menuId);

        if ($this->menu) {
            $this->isModalOpen = true;
        }
    }

    public function closeModal()
    {
        $this->menu = null;
        $this->isModalOpen = false;
    }

    public function render()
    {
        return view('livewire.menu-details-modal');
    }
}
