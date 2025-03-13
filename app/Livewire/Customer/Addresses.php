<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Address;
use Mary\Traits\Toast;
use Livewire\WithPagination;

class Addresses extends Component
{
    use Toast, WithPagination;
    
    public $showForm = false;
    public $editing = null;
    public $confirmingDelete = null;
    public $recipient_name;
    public $street_address;
    public $city;
    public $postal_code;
    public $country = 'Suisse';

    protected $rules = [
        'recipient_name' => 'required|string|max:255',
        'street_address' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'postal_code' => 'required|string|max:20',
        'country' => 'required|string|max:255'
    ];

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Address $address)
    {
        $this->ensureOwnership($address);
        $this->editing = $address->id;
        $this->fill($address->only([
            'recipient_name', 
            'street_address', 
            'city', 
            'postal_code', 
            'country'
        ]));
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Check max addresses only when creating new
        if (!$this->editing && auth()->user()->addresses()->count() >= 3) {
            $this->error("Maximum 3 adresses autorisées");
            return;
        }

        $data = $this->only([
            'recipient_name',
            'street_address',
            'city',
            'postal_code',
            'country'
        ]);

        if ($this->editing) {
            $address = Address::find($this->editing);
            $this->ensureOwnership($address);
            $address->update($data);
            $this->success("Adresse mise à jour");
        } else {
            auth()->user()->addresses()->create($data);
            $this->success("Adresse ajoutée");
        }

        $this->cancel();
    }

    public function confirmDelete($addressId)
    {
        $this->confirmingDelete = $addressId;
    }

    public function delete()
    {
        $address = Address::find($this->confirmingDelete);
        $this->ensureOwnership($address);
        
        $address->delete();
        $this->success("Adresse supprimée");
        $this->confirmingDelete = null;
    }

    public function cancel()
    {
        $this->reset([
            'showForm', 
            'editing', 
            'recipient_name', 
            'street_address', 
            'city', 
            'postal_code',
            'confirmingDelete'
        ]);
    }

    private function ensureOwnership($address)
    {
        if ($address && $address->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action');
        }
    }

    private function resetForm()
    {
        $this->reset([
            'recipient_name',
            'street_address',
            'city',
            'postal_code'
        ]);
    }

    public function render()
    {
        return view('livewire.customer.addresses', [
            'addresses' => auth()->user()->addresses()
                ->latest()
                ->paginate(5),
            'addressCount' => auth()->user()->addresses()->count()
        ]);
    }
}