<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{

    //Aggiorna la password dell'utente.
    public function update(Request $request): RedirectResponse
    {
        // Valida i dati in arrivo
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Aggiorna la password nel database (criptandola)
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Torna indietro con un messaggio di successo
        return back()->with('status', 'password-updated');
    }
}