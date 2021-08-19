<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

use App\Response\Response;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
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
        if (!$request->token) {
            return Response::unauthorized();
        }
        
        $user = User::where('token', $request->token)->first();

        if (!$user) {
            return Response::unauthorized();
        }

        Auth::login($user);

        return $next($request);
    }
}
