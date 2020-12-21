<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Usuario ou senha incorretos'], 401);
        }
        
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
    public function me() {
        $user =  auth('api')->user();
        if($user) {
            return response()->json(['user'=>$user]);
        }
        return response()->json(['message' => 'Erro ao encontrar usuario',404]);
    }

}
