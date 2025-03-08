<?php

namespace App\Livewire\Manager\Components\Users;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads, Toast;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $phone_number='';
    public string $role = 'customer'; 
    public $avatar;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'phone_number' => 'nullable|string|max:15|unique:users,phone_number',
        'role' => 'required|in:customer,delivery_person',
        'avatar' => 'nullable|image|max:2048',
    ];

    public function save()
        {
            $this->validate();

            try {
                $authenticatedUser = Auth::user();

                if (!$authenticatedUser) {
                    $this->error(
                        title: 'Erreur',
                        description: 'Vous devez être authentifié pour effectuer cette action.'
                    );
                    return;
                }

                
                if ($authenticatedUser->role === 'manager' && in_array($this->role, ['admin', 'manager'])) {
                    $this->error(
                        title: 'Accès refusé',
                        description: 'Les managers ne peuvent créer que des clients ou des livreurs.'
                    );
                    return;
                }

             
                $avatarUrl = $this->avatar
                    ? Cloudinary::upload($this->avatar->getRealPath())->getSecurePath()
                    : '/images/midnight_sushi/midnight_sushi_user.jpg'; 

        
                User::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'phone_number' => $this->phone_number?:null,
                    'role' => $this->role,
                    'avatar' => $avatarUrl,
                ]);

               
                $this->reset(['first_name', 'last_name', 'email', 'password', 'phone_number', 'role', 'avatar']);

                
                $this->success(
                    title: 'Utilisateur ajouté',
                    description: 'L\'utilisateur a été créé avec succès.',
                    redirectTo: route('manager.users.index')
                );
            } catch (\Exception $e) {

                Log::error('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(), // Adds detailed error trace
                    'user_input' => [
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'phone_number' => $this->phone_number,
                        'role' => $this->role,
                    ],
                ]);
            
                $this->error(
                    title: 'Erreur',
                    description: 'Une erreur est survenue : ' . $e->getMessage()
                );
            }
        }


    public function render()
    {
        return view('livewire.manager.components.users.create');
    }
}
