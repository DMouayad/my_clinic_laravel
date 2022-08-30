<?php

namespace App\Traits;

use App\Exceptions\EmailAlreadyRegisteredException;
use Exception;
use \Illuminate\Http\JsonResponse;


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
                'data' => $data,
                'status' => $status_code,
                'message' => $message,
            ],
            $status_code,
        );
    }
    /**
     * @param array|null $error
     * @param integer $status_code
     * @return \\Illuminate\Http\JsonResponse
     */
    protected function errorResponse(
        array|null $error = null,
        int $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR
    ) {
        return new JsonResponse(
            [
                'status' => $status_code,
                'error' => $error,
                'data' => null,
            ],
            $status_code,
        );
    }
    /**
     * @param Exception $e
     * @param string|null $message
     * @param integer $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponseFromException(
        Exception $e,
        string|null $message = null,
        int $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR
    ) {
        return new JsonResponse(
            [
                'error' => [
                    'exception' => get_class($e),
                    'message' => $message,
                ],
                'status' => $status_code,
            ],
            $status_code
        );
    }
    private function getExceptionStatusCode($exception)
    {
        $status_code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        switch (get_class($exception)) {
            case StaffEmailAlreadyExistsException::class:
                $status_code = JsonResponse::HTTP_CONFLICT;
                break;
            case EmailAlreadyRegisteredException::class:
                $status_code = JsonResponse::HTTP_CONFLICT;
                break;
            case RoleNotFoundException::class:
                $status_code = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
                break;
            case UnauthorizedToDeleteUserException::class:
                $status_code = JsonResponse::HTTP_UNAUTHORIZED;
                break;
        }
        return $status_code;
    }
}
