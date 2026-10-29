<?php

namespace App\Repositories\MySql;

use App\Constants\CommonConstant;
use App\Models\PasswordResetToken;
use App\Repositories\DAO\PasswordResetTokenDAO;
use App\Repositories\PasswordResetTokenRepository;
use Illuminate\Database\Eloquent\Collection;

class PasswordResetTokenRepositoryImpl implements PasswordResetTokenRepository
{
    public function insert(PasswordResetTokenDAO $passwordResetTokenDao): PasswordResetToken
    {
        return PasswordResetToken::create($passwordResetTokenDao->toArray());
    }

    public function findByEmail(string $email): Collection
    {
        return PasswordResetToken::where('email', $email)
            ->where('is_deleted', CommonConstant::IS_DELETED_NO)
            ->get();
    }

    public function updateById(int $id, PasswordResetTokenDAO $passwordResetTokenDao): bool
    {
        return PasswordResetToken::where('id', $id)->update($passwordResetTokenDao->toArray());
    }
}
