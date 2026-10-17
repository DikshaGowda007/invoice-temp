<?php

namespace App\Modules\V1\Invoice\Services\CreateOrder;

use App\Constants\InvoiceConstants;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Services\AuthService;
use App\Repositories\DAO\V1\InvoiceDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Utils\CommonUtils;
use Illuminate\Support\Collection;
use Razorpay\Api\Api;

class DetailsService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly AuthService $authService,
    ) {}

    public function createOrder(int $id): array
    {
        $userId = $this->authService->getData()->get('userId');
        $invoice = $this->findInvoiceOrFail($id, $userId);

        $this->validatePayable($invoice->get('status'));

        $amountInPaise = (int) round(((float) $invoice->get('total')) * 100);

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $order = $api->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => $invoice->get('invoice_number'),
            'notes' => [
                'invoice_id' => $id,
                'user_id' => $userId,
            ],
        ]);

        $invoiceDao = new InvoiceDAO;
        $invoiceDao->setRazorpayOrderId($order->id);
        $this->invoiceRepository->updateById($id, $invoiceDao);

        return CommonUtils::successDataResponse([
            'order_id' => $order->id,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key_id' => config('services.razorpay.key_id'),
            'invoice_number' => $invoice->get('invoice_number'),
        ]);
    }

    private function findInvoiceOrFail(int $id, int $userId): Collection
    {
        $invoice = $this->invoiceRepository->findByIdAndUserId($id, $userId)->first();

        if (! $invoice) {
            throw DataNotFoundException::withMessage('Invoice not found.');
        }

        return collect($invoice);
    }

    private function validatePayable(string $status): void
    {
        if (! in_array($status, [InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_OVERDUE], true)) {
            throw InvalidDataException::withMessage('Only sent or overdue invoices can be paid.');
        }
    }
}
