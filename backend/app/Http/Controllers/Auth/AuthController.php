<?php

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatusConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Modules\Auth\Register\Services\RegisterService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $registerService = app(RegisterService::class);
        $registerBo = $registerService->prepareBo($registerRequest);

        return response()->json($registerService->add($registerBo), HttpStatusConstant::CREATED);
    }
}
