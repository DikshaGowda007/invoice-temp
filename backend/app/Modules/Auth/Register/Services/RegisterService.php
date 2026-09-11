<?php

namespace App\Modules\Auth\Register\Services;

use App\Constants\CommonConstant;
use App\Http\Requests\Auth\RegisterRequest;
use App\Modules\Auth\JwtService;
use App\Modules\Auth\Register\Bo\RegisterBo;
use App\Repositories\DAO\UserDAO;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function __construct(
        private RegisterBo $registerBo,
        private UserDAO $userDao,
        private readonly UserRepository $userRepository,
    ) {}

    public function prepareBo(RegisterRequest $registerRequest): RegisterBo
    {
        $this->registerBo->setFirstName($registerRequest->string('first_name')->toString());
        $this->registerBo->setLastName($registerRequest->string('last_name')->toString());
        $this->registerBo->setEmail($registerRequest->string('email')->toString());
        $this->registerBo->setPassword($registerRequest->string('password')->toString());

        return $this->registerBo;
    }

    public function add(RegisterBo $registerBo): array
    {
        $this->registerBo = $registerBo;

        $user = collect($this->userRepository->insert($this->prepareDao()));
        $token = $this->createToken($user);
        $this->queueCookie($token);

        return [
            'status' => CommonConstant::SUCCESS,
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ],
        ];
    }

    public function prepareDao(): UserDAO
    {
        $this->userDao->setFirstName($this->registerBo->getFirstName());
        $this->userDao->setLastName($this->registerBo->getLastName());
        $this->userDao->setEmail($this->registerBo->getEmail());
        $this->userDao->setPassword(Hash::make($this->registerBo->getPassword()));

        return $this->userDao;
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
