<?php

namespace App\Constants;

class RecurringInvoiceConstants
{
    public const FREQUENCY_WEEKLY = 'WEEKLY';

    public const FREQUENCY_MONTHLY = 'MONTHLY';

    public const FREQUENCY_QUARTERLY = 'QUARTERLY';

    public const FREQUENCY_YEARLY = 'YEARLY';

    public const VALID_FREQUENCIES = [
        self::FREQUENCY_WEEKLY,
        self::FREQUENCY_MONTHLY,
        self::FREQUENCY_QUARTERLY,
        self::FREQUENCY_YEARLY,
    ];

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_PAUSED = 'PAUSED';

    public const STATUS_CANCELLED = 'CANCELLED';

    public const VALID_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_PAUSED,
        self::STATUS_CANCELLED,
    ];

    public const DEFAULT_AMOUNT = 0.0;
}
