<?php

namespace App\Http\Controllers\Invoice;

use App\Constants\HttpStatusConstant;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Invoice\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\Invoice\Clone\DetailsRequest as CloneDetailsRequest;
use App\Http\Requests\V1\Invoice\Delete\DetailsRequest as DeleteDetailsRequest;
use App\Http\Requests\V1\Invoice\Edit\DetailsRequest as EditDetailsRequest;
use App\Http\Requests\V1\Invoice\Get\DetailsRequest as GetDetailsRequest;
use App\Http\Requests\V1\Invoice\LineItem\Add\DetailsRequest as AddLineItemDetailsRequest;
use App\Http\Requests\V1\Invoice\LineItem\Delete\DetailsRequest as DeleteLineItemDetailsRequest;
use App\Http\Requests\V1\Invoice\LineItem\Update\DetailsRequest as UpdateLineItemDetailsRequest;
use App\Http\Requests\V1\Invoice\List\DetailsRequest as ListDetailsRequest;
use App\Http\Requests\V1\Invoice\UpdateStatus\DetailsRequest as UpdateStatusDetailsRequest;
use App\Modules\V1\Invoice\Services\Add\DetailsService as AddDetailsService;
use App\Modules\V1\Invoice\Services\Clone\DetailsService as CloneDetailsService;
use App\Modules\V1\Invoice\Services\Delete\DetailsService as DeleteDetailsService;
use App\Modules\V1\Invoice\Services\Edit\DetailsService as EditDetailsService;
use App\Modules\V1\Invoice\Services\Get\DetailsService as GetDetailsService;
use App\Modules\V1\Invoice\Services\LineItem\Add\DetailsService as AddLineItemDetailsService;
use App\Modules\V1\Invoice\Services\LineItem\Delete\DetailsService as DeleteLineItemDetailsService;
use App\Modules\V1\Invoice\Services\LineItem\Update\DetailsService as UpdateLineItemDetailsService;
use App\Modules\V1\Invoice\Services\List\DetailsService as ListDetailsService;
use App\Modules\V1\Invoice\Services\UpdateStatus\DetailsService as UpdateStatusDetailsService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Throwable;

class InvoiceController extends Controller
{
    public function list(ListDetailsRequest $listDetailsRequest): JsonResponse
    {
        try {
            $listDetailsService = app(ListDetailsService::class);

            return response()->json(
                $listDetailsService->list(),
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

    public function add(AddDetailsRequest $addDetailsRequest): JsonResponse
    {
        try {
            $addDetailsService = app(AddDetailsService::class);
            $addDetailsBo = $addDetailsService->prepareBo($addDetailsRequest);

            return response()->json(
                $addDetailsService->add($addDetailsBo),
                HttpStatusConstant::CREATED,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function clone(CloneDetailsRequest $cloneDetailsRequest): JsonResponse
    {
        try {
            $cloneDetailsService = app(CloneDetailsService::class);
            $invoiceId = (int) $cloneDetailsRequest->input('id');

            return response()->json(
                $cloneDetailsService->clone($invoiceId),
                HttpStatusConstant::CREATED,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function get(GetDetailsRequest $getDetailsRequest): JsonResponse
    {
        try {
            $getDetailsService = app(GetDetailsService::class);
            $invoiceId = (int) $getDetailsRequest->input('id');

            return response()->json(
                $getDetailsService->get($invoiceId),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function edit(EditDetailsRequest $editDetailsRequest): JsonResponse
    {
        try {
            $editDetailsService = app(EditDetailsService::class);
            $editDetailsBo = $editDetailsService->prepareBo($editDetailsRequest);

            return response()->json(
                $editDetailsService->edit($editDetailsBo),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function delete(DeleteDetailsRequest $deleteDetailsRequest): JsonResponse
    {
        try {
            $deleteDetailsService = app(DeleteDetailsService::class);
            $invoiceId = (int) $deleteDetailsRequest->input('id');

            return response()->json(
                $deleteDetailsService->delete($invoiceId),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateStatus(UpdateStatusDetailsRequest $updateStatusDetailsRequest): JsonResponse
    {
        try {
            $updateStatusDetailsService = app(UpdateStatusDetailsService::class);
            $updateStatusDetailsBo = $updateStatusDetailsService->prepareBo($updateStatusDetailsRequest);

            return response()->json(
                $updateStatusDetailsService->updateStatus($updateStatusDetailsBo),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (InvalidDataException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function addLineItem(AddLineItemDetailsRequest $addLineItemDetailsRequest): JsonResponse
    {
        try {
            $addLineItemDetailsService = app(AddLineItemDetailsService::class);
            $addLineItemDetailsBo = $addLineItemDetailsService->prepareBo($addLineItemDetailsRequest);

            return response()->json(
                $addLineItemDetailsService->add($addLineItemDetailsBo),
                HttpStatusConstant::CREATED,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateLineItem(UpdateLineItemDetailsRequest $updateLineItemDetailsRequest): JsonResponse
    {
        try {
            $updateLineItemDetailsService = app(UpdateLineItemDetailsService::class);
            $updateLineItemDetailsBo = $updateLineItemDetailsService->prepareBo($updateLineItemDetailsRequest);

            return response()->json(
                $updateLineItemDetailsService->update($updateLineItemDetailsBo),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function deleteLineItem(DeleteLineItemDetailsRequest $deleteLineItemDetailsRequest): JsonResponse
    {
        try {
            $deleteLineItemDetailsService = app(DeleteLineItemDetailsService::class);
            $lineItemId = (int) $deleteLineItemDetailsRequest->input('line_item_id');

            return response()->json(
                $deleteLineItemDetailsService->delete($lineItemId),
                HttpStatusConstant::OK,
            );
        } catch (DataNotFoundException $e) {
            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::NOT_FOUND,
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
