<?php

namespace App\Modules\V1\Invoice\Services\PublicCreateOrder;

use App\Constants\InvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Razorpay\Api\Api;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function createOrder(string $token): array
    {
        $invoice = $this->findInvoiceOrFail($token);

        $this->validatePayable($invoice->get('status'));

        $amountInPaise = (int) round(((float) $invoice->get('total')) * 100);

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $order = $api->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => $invoice->get('invoice_number'),
            'notes' => [
                'invoice_id' => $invoice->get('id'),
                'user_id' => $invoice->get('user_id'),
            ],
        ]);

        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setRazorpayOrderId($order->id);
        $this->invoiceRepository->updateById((int) $invoice->get('id'), $invoiceDao);

        return CommonUtils::successDataResponse([
            'order_id' => $order->id,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key_id' => config('services.razorpay.key_id'),
            'invoice_number' => $invoice->get('invoice_number'),
        ]);
    }

    private function findInvoiceOrFail(string $token): Collection
    {
        $invoice = $this->invoiceRepository->findByPublicToken($token)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('This payment link is invalid or has expired.');
        }

        $invoice = collect($invoice);

        if ($this->isExpired($invoice)) {
            throw DataNotFoundException::withMessage('This payment link is invalid or has expired.');
        }

        return $invoice;
    }

    private function isExpired(Collection $invoice): bool
    {
        $expiresAt = $invoice->get('public_token_expires_at');

        if (! $expiresAt) {
            return false;
        }

        return Carbon::parse($expiresAt)->isPast();
    }

    private function validatePayable(string $status): void
    {
        if (! in_array($status, [InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_OVERDUE], true)) {
            throw InvalidDataException::withMessage('Only sent or overdue invoices can be paid.');
        }
    }
}
