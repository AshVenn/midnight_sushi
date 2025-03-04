<?php

namespace App\Livewire\Manager\Components\Menus;

use App\Models\Menu;
use Livewire\Component;

class Show extends Component
{
    public Menu $menu;

    public function render()
    {
        return view('livewire.admin.components.menus.show');
    }
}
