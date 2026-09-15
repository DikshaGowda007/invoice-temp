<?php

namespace App\Modules\Auth\Login\Services;

use App\Constants\CommonConstant;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Modules\Auth\JwtService;
use App\Repositories\DAO\UserDAO;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function add(string $email, string $password): array
    {
        $user = collect($this->validateUser($email, $password));
        $token = $this->createToken($user);
        $this->updateUserLastLoginTime((int) $user->get('id'));
        $this->queueCookie($token);

        return [
            'status' => CommonConstant::SUCCESS,
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ],
        ];
    }

    private function validateUser(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $user;
    }

    private function createToken(Collection $user): string
    {
        return JwtService::generateToken([
            'is_loggedin' => 1,
            'loggedin_user_id' => $user->get('id'),
            'loggedin_user_first_name' => $user->get('first_name'),
            'loggedin_user_last_name' => $user->get('last_name'),
            'loggedin_user_email' => $user->get('email'),
        ]);
    }

    private function updateUserLastLoginTime(int $userId): void
    {
        $userDao = new UserDAO;
        $userDao->setLastLogin(Carbon::now()->format('Y-m-d H:i:s'));
        $this->userRepository->updateById($userId, $userDao);
    }

    private function formatUser(Collection $user): array
    {
        return [
            'id' => $user->get('id'),
            'name' => $user->get('first_name').' '.$user->get('last_name'),
            'email' => $user->get('email'),
        ];
    }

    private function queueCookie(string $token): void
    {
        Cookie::queue('token', $token, 60 * 24, '/', null, true, true, false, 'Strict');
    }
}
