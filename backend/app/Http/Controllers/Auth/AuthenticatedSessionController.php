<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Procesa la autenticación del usuario.
     *
     * @param LoginRequest $request Request con credenciales validadas.
     * @return Response
     *
     * @throws \Illuminate\Validation\ValidationException Si las credenciales no son válidas.
     */
    public function store(LoginRequest $request): Response
    {
        logger()->info('login.store called', ['email' => $request->input('email')]);

        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Cierra la sesión autenticada.
     *
     * @param Request $request Request actual.
     * @return Response
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
