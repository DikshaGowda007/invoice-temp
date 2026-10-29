<?php

namespace App\Modules\Auth\ForgotPassword\Services;

use App\Constants\CommonConstant;
use App\Mail\PasswordResetMail;
use App\Repositories\DAO\PasswordResetTokenDAO;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordService
{
    private const GENERIC_MESSAGE = 'If an account exists for that email, a password reset link has been sent.';

    private const RESET_TOKEN_LENGTH = 64;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordResetTokenRepository $passwordResetTokenRepository,
    ) {}

    public function send(string $email): array
    {
        $user = $this->findUser($email);

        if ($user) {
            $plainToken = $this->issueToken($email);
            $this->sendResetEmail($email, $user, $plainToken);
        }

        return ['status' => CommonConstant::SUCCESS, 'data' => self::GENERIC_MESSAGE];
    }

    private function findUser(string $email): ?object
    {
        return $this->userRepository->findByEmail($email)->first();
    }

    private function issueToken(string $email): string
    {
        $plainToken = Str::random(self::RESET_TOKEN_LENGTH);
        $passwordResetTokenDao = $this->prepareDao($email, $plainToken);
        $existing = $this->passwordResetTokenRepository->findByEmail($email)->first();

        if ($existing) {
            $this->passwordResetTokenRepository->updateById($existing->id, $passwordResetTokenDao);
        } else {
            $this->passwordResetTokenRepository->insert($passwordResetTokenDao);
        }

        return $plainToken;
    }

    private function prepareDao(string $email, string $plainToken): PasswordResetTokenDAO
    {
        return (new PasswordResetTokenDAO)
            ->setEmail($email)
            ->setToken(Hash::make($plainToken))
            ->setCreatedAt(Carbon::now()->toDateTimeString())
            ->setIsDeleted(CommonConstant::IS_DELETED_NO);
    }

    private function sendResetEmail(string $email, object $user, string $plainToken): void
    {
        $resetUrl = $this->buildResetUrl($email, $plainToken);

        Mail::to($email)->send(new PasswordResetMail($user->first_name, $resetUrl));
    }

    private function buildResetUrl(string $email, string $plainToken): string
    {
        return sprintf(
            '%s/reset-password?token=%s&email=%s',
            config('frontend.url'),
            $plainToken,
            urlencode($email),
        );
    }
}
