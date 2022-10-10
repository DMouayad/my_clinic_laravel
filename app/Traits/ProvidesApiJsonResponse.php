<?php

namespace App\Traits;

use App\Exceptions\CustomException;
use App\Models\CustomError;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;
trait ProvidesApiJsonResponse 
{
    /**
     * @param array|null $data
     * @param string|null $message
     * @param integer $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(
        JsonResource|array|null $data = null,
        string|null $message = null,
        int $status_code = 200
    ) {
        return new JsonResponse(
            [
                "data" => $data,
                "status" => $status_code,
                "message" => $message,
            ],
            $status_code
        );
    }

    /**
     * @param \App\Exceptions\CustomException $e
     * @param string|null $message
     * @param integer $status_code
     * @param string|array|null $description
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponseFromException(
        CustomException $e,
        string|null $message = null,
        int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR,
        string|array $description = null
    ) {
        return $this->errorResponse(
            new CustomError(
                message: $message ?? $e->getMessage(),
                code: $e->getCode(),
                exception: $e::className(),
                description: $description
            ),
            $status_code
        );
    }

    /**
     *
     * @param \App\Models\CustomError|null $error
     * @param int $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(
        ?CustomError $error = null,
        int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
        return new JsonResponse(
            [
                "error" => $error,
                "status" => $status_code,
            ],
            $status_code
        );
    }
}
