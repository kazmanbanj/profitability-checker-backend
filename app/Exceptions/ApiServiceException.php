<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiServiceException extends Exception
{
    public function __construct(
        protected string $msg = 'A downstream service error occurred. Please try again later.'
    ) {
        parent::__construct($this->msg);
    }

    public function render(): JsonResponse
    {
        return (new ApiResponse(
            message: $this->msg,
            error: $this->msg,
        ))->asServerError();
    }
}
