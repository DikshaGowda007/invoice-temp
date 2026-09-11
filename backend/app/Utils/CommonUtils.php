<?php

namespace App\Utils;

use App\Constants\CommonConstant;

class CommonUtils
{
    public static function successDataResponse(array $data): array
    {
        return ['status' => CommonConstant::SUCCESS, 'data' => $data];
    }
}
