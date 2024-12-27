<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;

class MenuDetailsModal extends Component
{
    public ?Menu $menu = null;
    public bool $isModalOpen = false;

    /**
     * This component listens for the "openMenuModal" event.
     * That event is dispatched from MenusProducts->showMenuDetails($menuId).
     */
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
        $this->isModalOpen = false;  // Toggle the modal OFF
    }

    /**
     * If the user clicks "Ajouter au Panier" inside the modal,
     * dispatch an event so that another component (e.g. MenusProducts) can update the cart.
     */
    public function addToCart($type, $id)
    {
        $this->dispatch('addToCart', $type, $id);
    }

    public function render()
    {
        return view('livewire.menu-details-modal');
    }
}
