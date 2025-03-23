<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class VerificationController extends Controller
{
    use Toast;

    public function __invoke(EmailVerificationRequest $request)
    {
        $user = $request->user();
        
        if (!$user) {
            $user = User::find($request->route('id'));
            Auth::login($user);
        }

        $request->fulfill();

        return redirect('/')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Email vérifié',
                'description' => 'Votre email a été vérifié avec succès! Vous êtes maintenant connecté.'
            ]);
    }
}