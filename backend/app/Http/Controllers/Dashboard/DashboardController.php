<?php

namespace App\Http\Controllers\Dashboard;

use App\Constants\HttpStatusConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Dashboard\Summary\DetailsRequest as SummaryDetailsRequest;
use App\Modules\V1\Dashboard\Services\Summary\DetailsService as SummaryDetailsService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Throwable;

class DashboardController extends Controller
{
    public function summary(SummaryDetailsRequest $summaryDetailsRequest): JsonResponse
    {
        try {
            $summaryDetailsService = app(SummaryDetailsService::class);
            $summaryDetailsBo = $summaryDetailsService->prepareBo($summaryDetailsRequest);

            return response()->json(
                $summaryDetailsService->summary($summaryDetailsBo),
                HttpStatusConstant::OK,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }
}
