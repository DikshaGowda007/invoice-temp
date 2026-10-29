<?php

namespace App\Modules\Auth\ResetPassword\Services;

use App\Constants\CommonConstant;
use App\Exceptions\Auth\InvalidResetTokenException;
use App\Repositories\DAO\PasswordResetTokenDAO;
use App\Repositories\DAO\UserDAO;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class ResetPasswordService
{
    private const TOKEN_TTL_MINUTES = 60;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordResetTokenRepository $passwordResetTokenRepository,
    ) {}

    public function reset(string $email, string $token, string $password): array
    {
        $user = $this->findUser($email);
        $resetToken = $this->verifyToken($email, $token);

        if (! $user || ! $resetToken) {
            throw new InvalidResetTokenException;
        }

        $this->updatePassword($user->id, $password);
        $this->invalidateToken($resetToken->id);

        return ['status' => CommonConstant::SUCCESS, 'data' => 'Your password has been reset. You can now log in.'];
    }

    private function findUser(string $email): ?object
    {
        return $this->userRepository->findByEmail($email)->first();
    }

    private function verifyToken(string $email, string $plainToken): ?object
    {
        $resetToken = $this->passwordResetTokenRepository->findByEmail($email)->first();

        if (! $resetToken || $this->isTokenExpired($resetToken) || ! Hash::check($plainToken, $resetToken->token)) {
            return null;
        }

        return $resetToken;
    }

    private function isTokenExpired(object $resetToken): bool
    {
        return Carbon::parse($resetToken->created_at)->addMinutes(self::TOKEN_TTL_MINUTES)->isPast();
    }

    private function updatePassword(int $userId, string $password): void
    {
        $this->userRepository->updateById($userId, $this->prepareUserDao($password));
    }

    private function prepareUserDao(string $password): UserDAO
    {
        return (new UserDAO)->setPassword(Hash::make($password));
    }

    private function invalidateToken(int $tokenId): void
    {
        $this->passwordResetTokenRepository->updateById($tokenId, $this->prepareTokenDao());
    }

    private function prepareTokenDao(): PasswordResetTokenDAO
    {
        return (new PasswordResetTokenDAO)->setIsDeleted(CommonConstant::IS_DELETED_YES);
    }
}
