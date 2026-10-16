<?php

namespace App\Constants;

class InvoiceActivityConstants
{
    public const TYPE_PAID = 'PAID';

    public const TYPE_RECEIPT_SENT = 'RECEIPT_SENT';

    public const TYPE_RECEIPT_FAILED = 'RECEIPT_FAILED';

    public const TYPE_INVOICE_EMAILED = 'INVOICE_EMAILED';

    public const TYPE_INVOICE_EMAIL_FAILED = 'INVOICE_EMAIL_FAILED';

    public const TYPE_OVERDUE_REMINDER_SENT = 'OVERDUE_REMINDER_SENT';

    public const TYPE_OVERDUE_REMINDER_FAILED = 'OVERDUE_REMINDER_FAILED';

    public const TYPE_CREATED = 'CREATED';

    public const TYPE_EDITED = 'EDITED';

    public const TYPE_DELETED = 'DELETED';

    public const TYPE_LINE_ITEM_ADDED = 'LINE_ITEM_ADDED';

    public const TYPE_LINE_ITEM_UPDATED = 'LINE_ITEM_UPDATED';

    public const TYPE_LINE_ITEM_DELETED = 'LINE_ITEM_DELETED';
}
