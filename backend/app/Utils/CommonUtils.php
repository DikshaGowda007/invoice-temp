<?php

namespace App\Utils;

use App\Constants\CommonConstant;

class CommonUtils
{
    public static function errorResponse(string $message): array
    {
        return ['status' => CommonConstant::ERROR, 'message' => $message];
    }

    public static function successDataResponse(array $data): array
    {
        return ['status' => CommonConstant::SUCCESS, 'data' => $data];
    }

    public static function successResponse(string $message): array
    {
        return ['status' => CommonConstant::SUCCESS, 'data' => $message];
    }
}
