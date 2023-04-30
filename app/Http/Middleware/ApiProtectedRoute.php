<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class ApiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
      } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        // do whatever you want to do if a token is expired
        $newToken = JWTAuth::parseToken()->refresh();
        return response()->json(['message' => 'Token is Expired', 'token' => $newToken], 401);
      } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        // do whatever you want to do if a token is invalid
        return response()->json(['message' => 'Token is Invalid'], 401);
      } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        // do whatever you want to do if a token is not present
        return response()->json(['message' => 'Authorization Token is not found.'], 401);
      }

      return $next($request);
    }
}
