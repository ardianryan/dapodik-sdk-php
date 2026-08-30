<?php

namespace Smansage\Dapodik\Exceptions;

class DapodikHttpException extends DapodikException
{
    public function __construct(
        int $statusCode,
        string $statusText,
        string $endpoint,
        public readonly string $rawBody = ''
    ) {
        parent::__construct(
            "Dapodik WebService HTTP {$statusCode} ({$statusText}) saat memanggil endpoint '{$endpoint}'",
            $statusCode
        );
    }
}
