<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;
use App\Repositories\DAO\PasswordResetTokenDAO;
use Illuminate\Database\Eloquent\Collection;

interface PasswordResetTokenRepository
{
    public function insert(PasswordResetTokenDAO $passwordResetTokenDao): PasswordResetToken;

    public function findByEmail(string $email): Collection;

    public function updateById(int $id, PasswordResetTokenDAO $passwordResetTokenDao): bool;
}
