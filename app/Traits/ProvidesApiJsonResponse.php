<?php

namespace App\Traits;

use App\Exceptions\CustomException;
use App\Models\CustomError;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ProvidesApiJsonResponse
{
    /**
     * @param array|null $data
     * @param string|null $message
     * @param integer $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(
        array|null $data = null,
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
     *
     * @param [type] $status_code
     * @param \App\Models\CustomError|null ...$errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(
        int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?CustomError ...$errors
    ) {
        return new JsonResponse(
            [
                "errors" => $errors,
                "status" => $status_code,
            ],
            $status_code
        );
    }

    /**
     * @param \App\Exceptions\CustomException $e
     * @param string|null $message
     * @param integer $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponseFromException(
        CustomException $e,
        string|null $message = null,
        int $status_code = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
        return $this->errorResponse(
            $status_code,
            new CustomError(
                message: $message ?? $e->getMessage(),
                code: $e->getCode(),
                exception: $e::className(),
            )
        );
    }
}
