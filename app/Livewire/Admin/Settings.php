<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use Mary\Traits\Toast;

class Settings extends Component
{
    use Toast;
    
    public $deliveryFee;

    public function mount()
    {
        $this->deliveryFee = Setting::getValue('delivery_fee', 0);
    }

    public function save()
    {
        $this->validate([
            'deliveryFee' => 'required|numeric|min:0'
        ]);

        Setting::setValue('delivery_fee', $this->deliveryFee);
        
        $this->success(
            'Paramètres mis à jour', 
            'Les frais de livraison ont été modifiés avec succès.'
        );
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}