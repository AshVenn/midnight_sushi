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
        if (auth()->user()->addresses()->count() >= 3) {
            $this->error("Maximum 3 adresses autorisées");
            return;
        }
        
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Address $address)
    {
        $this->editing = $address->id;
        $this->recipient_name = $address->recipient_name;
        $this->street_address = $address->street_address;
        $this->city = $address->city;
        $this->postal_code = $address->postal_code;
        $this->country = $address->country;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'recipient_name' => $this->recipient_name,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country
        ];

        if ($this->editing) {
            auth()->user()->addresses()->find($this->editing)->update($data);
            $this->success("Adresse mise à jour");
        } else {
            auth()->user()->addresses()->create($data);
            $this->success("Adresse ajoutée");
        }

        $this->cancel();
    }

    public function delete(Address $address)
    {
        $address->delete();
        $this->success("Adresse supprimée");
    }

    public function cancel()
    {
        $this->reset(['showForm', 'editing', 'recipient_name', 'street_address', 'city', 'postal_code']);
    }

    public function render()
    {
        return view('livewire.customer.addresses', [
            'addresses' => auth()->user()->addresses()->latest()->paginate(5)
        ]);
    }
}