<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtXRefreshMiddleware extends BaseMiddleware
{

    use ApiResponser;

    public function handle($request, Closure $next)
    {
        $payload = JWTAuth::payload();

        if ( $payload->get('xtype') != 'refresh' ){
            return $this->errorResponse('Token Misused', 401);
        }

        return $next($request);
    }
}
