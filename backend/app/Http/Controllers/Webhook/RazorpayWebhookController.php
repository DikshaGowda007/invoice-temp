<?php

namespace App\Http\Controllers\Webhook;

use App\Constants\HttpStatusConstant;
use App\Http\Controllers\Controller;
use App\Modules\V1\Invoice\Services\WebhookMarkPaid\DetailsService as WebhookMarkPaidDetailsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Utility;
use Throwable;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature', '');
        $webhookSecret = (string) config('services.razorpay.webhook_secret');

        try {
            (new Utility)->verifyWebhookSignature($rawBody, $signature, $webhookSecret);
        } catch (SignatureVerificationError $e) {
            Log::warning('razorpay.webhook.invalid_signature', ['message' => $e->getMessage()]);

            return response()->json(
                ['status' => 'error', 'message' => 'Invalid signature.'],
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        $payload = json_decode($rawBody, true) ?? [];
        $event = $payload['event'] ?? null;

        try {
            if ($event === 'payment.captured') {
                $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
                $orderId = $paymentEntity['order_id'] ?? null;
                $paymentId = $paymentEntity['id'] ?? null;

                if ($orderId && $paymentId) {
                    app(WebhookMarkPaidDetailsService::class)->markPaidFromOrderId($orderId, $paymentId);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return response()->json(['status' => 'ok'], HttpStatusConstant::OK);
    }
}
