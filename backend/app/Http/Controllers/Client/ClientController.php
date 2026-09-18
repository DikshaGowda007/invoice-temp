<?php

namespace App\Http\Controllers\Client;

use App\Constants\HttpStatusConstant;
use App\Exceptions\DataNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Client\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\Client\Delete\DetailsRequest as DeleteDetailsRequest;
use App\Http\Requests\V1\Client\Edit\DetailsRequest as EditDetailsRequest;
use App\Http\Requests\V1\Client\Get\DetailsRequest as GetDetailsRequest;
use App\Http\Requests\V1\Client\List\DetailsRequest as ListDetailsRequest;
use App\Modules\V1\Client\Services\Add\DetailsService as AddDetailsService;
use App\Modules\V1\Client\Services\Delete\DetailsService as DeleteDetailsService;
use App\Modules\V1\Client\Services\Edit\DetailsService as EditDetailsService;
use App\Modules\V1\Client\Services\Get\DetailsService as GetDetailsService;
use App\Modules\V1\Client\Services\List\DetailsService as ListDetailsService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Throwable;

class ClientController extends Controller
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

    public function get(GetDetailsRequest $getDetailsRequest): JsonResponse
    {
        try {
            $getDetailsService = app(GetDetailsService::class);
            $clientId = (int) $getDetailsRequest->input('id');

            return response()->json(
                $getDetailsService->get($clientId),
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
            $clientId = (int) $deleteDetailsRequest->input('id');

            return response()->json(
                $deleteDetailsService->delete($clientId),
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
