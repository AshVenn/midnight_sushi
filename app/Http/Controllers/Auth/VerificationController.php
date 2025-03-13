<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        $user = $request->user();
        
        if (!$user) {
            $user = User::find($request->route('id'));
            Auth::login($user);
        }

        $request->fulfill();

        return redirect('/')
            ->with('success', 'Email verified successfully! You are now logged in.');
    }
    }