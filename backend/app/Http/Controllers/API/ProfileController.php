<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Actualiza el perfil (nombre y email) del usuario autenticado.
     *
     * @param Request $request Datos del perfil.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException Si la validación falla.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validar campos del perfil
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        // Guardar cambios
        $user->update($data);

        return response()->json($user);
    }

    /**
     * Actualiza la contraseña del usuario autenticado verificando la actual.
     *
     * @param Request $request Password actual y nueva.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException Si la contraseña actual no coincide o la validación falla.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // Validar contraseña actual y nueva
        $data = $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Verificar contraseña actual
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('The provided password does not match our records.')],
            ]);
        }

        // Actualizar password
        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return response()->json(['status' => 'password-updated']);
    }
}
