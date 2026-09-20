<?php

namespace App\Http\Controllers\RecurringInvoice;

use App\Constants\ErrorResponseConstant;
use App\Constants\HttpStatusConstant;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\InvalidDataException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RecurringInvoice\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\Clone\DetailsRequest as CloneDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\Delete\DetailsRequest as DeleteDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\Edit\DetailsRequest as EditDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\Get\DetailsRequest as GetDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\LineItem\Add\DetailsRequest as AddLineItemDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\LineItem\Delete\DetailsRequest as DeleteLineItemDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\LineItem\Update\DetailsRequest as UpdateLineItemDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\List\DetailsRequest as ListDetailsRequest;
use App\Http\Requests\V1\RecurringInvoice\UpdateStatus\DetailsRequest as UpdateStatusDetailsRequest;
use App\Modules\V1\RecurringInvoice\Services\Add\DetailsService as AddDetailsService;
use App\Modules\V1\RecurringInvoice\Services\Clone\DetailsService as CloneDetailsService;
use App\Modules\V1\RecurringInvoice\Services\Delete\DetailsService as DeleteDetailsService;
use App\Modules\V1\RecurringInvoice\Services\Edit\DetailsService as EditDetailsService;
use App\Modules\V1\RecurringInvoice\Services\Get\DetailsService as GetDetailsService;
use App\Modules\V1\RecurringInvoice\Services\LineItem\Add\DetailsService as AddLineItemDetailsService;
use App\Modules\V1\RecurringInvoice\Services\LineItem\Delete\DetailsService as DeleteLineItemDetailsService;
use App\Modules\V1\RecurringInvoice\Services\LineItem\Update\DetailsService as UpdateLineItemDetailsService;
use App\Modules\V1\RecurringInvoice\Services\List\DetailsService as ListDetailsService;
use App\Modules\V1\RecurringInvoice\Services\UpdateStatus\DetailsService as UpdateStatusDetailsService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Throwable;

class RecurringInvoiceController extends Controller
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function clone(CloneDetailsRequest $cloneDetailsRequest): JsonResponse
    {
        try {
            $cloneDetailsService = app(CloneDetailsService::class);
            $id = (int) $cloneDetailsRequest->input('id');

            return response()->json(
                $cloneDetailsService->clone($id),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function get(GetDetailsRequest $getDetailsRequest): JsonResponse
    {
        try {
            $getDetailsService = app(GetDetailsService::class);
            $id = (int) $getDetailsRequest->input('id');

            return response()->json(
                $getDetailsService->get($id),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function delete(DeleteDetailsRequest $deleteDetailsRequest): JsonResponse
    {
        try {
            $deleteDetailsService = app(DeleteDetailsService::class);
            $id = (int) $deleteDetailsRequest->input('id');

            return response()->json(
                $deleteDetailsService->delete($id),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateStatus(UpdateStatusDetailsRequest $updateStatusDetailsRequest): JsonResponse
    {
        try {
            $updateStatusDetailsService = app(UpdateStatusDetailsService::class);
            $id = (int) $updateStatusDetailsRequest->input('id');
            $status = (string) $updateStatusDetailsRequest->input('status');

            return response()->json(
                $updateStatusDetailsService->updateStatus($id, $status),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
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
                CommonUtils::errorResponse(ErrorResponseConstant::ERROR_MESSAGE_GENERAL),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }
}
