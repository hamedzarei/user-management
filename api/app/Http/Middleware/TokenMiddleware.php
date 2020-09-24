<?php

namespace App\Http\Middleware;

use App\Http\Services\TokenService;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('token')) throw new UnauthorizedException();
        $token_str = $request->header('token');
        $validity = TokenService::validate($token_str);
        if (!$validity) throw new UnauthorizedException();
        $data = TokenService::parse($token_str);
        $request->headers->set('x-user-id', $data['claims']['uid']);
        return $next($request);
    }
}
