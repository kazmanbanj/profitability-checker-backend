<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiValidationException extends Exception
{
    /**
     * @param string       $msg    Human-readable summary shown in the `message` field.
     * @param array|string $errors Field-level errors shown in the `error` field.
     */
    public function __construct(
        protected string $msg = 'The given data was invalid.',
        protected array|string $errors = []
    ) {
        parent::__construct($this->msg);
    }

    public function render(): JsonResponse
    {
        return (new ApiResponse(
            message: $this->msg,
            error: $this->errors,
        ))->asValidationError();
    }
}
