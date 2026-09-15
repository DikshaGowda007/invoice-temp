<?php

namespace App\Modules\Auth\Logout\Services;

use App\Repositories\DAO\UserDAO;
use App\Repositories\UserRepository;
use App\Utils\CommonUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

class LogoutService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function logout(Request $request): array
    {
        $jwtUser = $request->attributes->get('jwtUser');

        if ($jwtUser && isset($jwtUser['loggedin_user_id'])) {
            $userDao = new UserDAO;
            $userDao->setLastLogin(Carbon::now()->format('Y-m-d H:i:s'));
            $this->userRepository->updateById((int) $jwtUser['loggedin_user_id'], $userDao);
        }

        Cookie::queue(Cookie::forget('token'));

        return CommonUtils::successResponse('Logged out successfully.');
    }
}
