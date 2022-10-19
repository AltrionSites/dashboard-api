<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponser;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['username', 'password']);
        if(!$token = auth()->attempt($credentials))
        {
            return $this->errorResponse('Error de usuario y/o contraseña.', Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::logout();
        return $this->successResponse(['message' => 'Sesión cerrada con éxito.']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'username' => auth()->user()->username,
                'firstname' => auth()->user()->firstname,
                'lastname' => auth()->user()->lastname,
            ]
        ]);
    }
}
