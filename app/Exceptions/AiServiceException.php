<?php

namespace App\Exceptions;

class AiServiceException extends ApiServiceException
{
    public function __construct(
        string $msg = 'The AI analysis service is currently unavailable. Please try again later.'
    ) {
        parent::__construct($msg);
    }
}
