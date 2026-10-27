<?php

namespace App\Constants;

class DashboardConstants
{
    public const RANGE_3M = '3M';

    public const RANGE_6M = '6M';

    public const RANGE_1Y = '1Y';

    public const RANGE_2Y = '2Y';

    public const VALID_RANGES = [
        self::RANGE_3M,
        self::RANGE_6M,
        self::RANGE_1Y,
        self::RANGE_2Y,
    ];

    public const RANGE_TO_MONTHS = [
        self::RANGE_3M => 3,
        self::RANGE_6M => 6,
        self::RANGE_1Y => 12,
        self::RANGE_2Y => 24,
    ];

    public const DEFAULT_RANGE = self::RANGE_6M;
}
