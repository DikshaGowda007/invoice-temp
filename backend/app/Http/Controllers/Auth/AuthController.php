<?php

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatusConstant;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Modules\Auth\Login\Services\LoginService;
use App\Modules\Auth\Logout\Services\LogoutService;
use App\Modules\Auth\Register\Services\RegisterService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $registerService = app(RegisterService::class);
        $registerBo = $registerService->prepareBo($registerRequest);

        return response()->json($registerService->add($registerBo), HttpStatusConstant::CREATED);
    }

    public function login(LoginRequest $loginRequest): JsonResponse
    {
        try {
            $loginService = app(LoginService::class);
            $email = $loginRequest->input('email');
            $password = $loginRequest->input('password');

            return response()->json($loginService->add($email, $password), HttpStatusConstant::OK);
        } catch (InvalidCredentialsException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $logoutService = app(LogoutService::class);

        return response()->json($logoutService->logout($request), HttpStatusConstant::OK);
    }
}
