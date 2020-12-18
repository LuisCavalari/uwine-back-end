<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiProtected extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $exception) {
            if($exception instanceof TokenExpiredException) {
                return response()->json(['message' => 'Token expirado'],);
            } else if ($exception instanceof TokenInvalidException) {
                return response()->json(['message' => 'Token invalido']);
            } else {
                return response(['message' => 'Token não encontrado']);
            }
        }
        return $next($request);
    }
}
