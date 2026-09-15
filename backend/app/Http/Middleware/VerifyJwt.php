<?php

namespace App\Http\Middleware;

use App\Constants\HttpStatusConstant;
use App\Modules\Auth\JwtService;
use App\Utils\CommonUtils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->cookie('token');

        if (! $token) {
            return response()->json(
                CommonUtils::errorResponse('Token not provided.'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        $decoded = JwtService::decodeToken($token);

        if (! $decoded) {
            return response()->json(
                CommonUtils::errorResponse('Invalid or expired token.'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        $request->attributes->set('jwtUser', $decoded);

        return $next($request);
    }
}
