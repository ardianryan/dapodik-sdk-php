<?php

namespace Smansage\Dapodik;

class Dapodik
{
    public const DEFAULT_HOST = '127.0.0.1';
    public const DEFAULT_PORT = 5774;

    public function __construct(
        protected ?string $host = null,
        protected int|string|null $port = null,
        protected float $timeout = 30.0
    ) {
        $this->host = $this->host ?: self::DEFAULT_HOST;
        $this->port = $this->port ?: self::DEFAULT_PORT;
    }

    /**
     * Membuat instance WebService Client dengan Token dan NPSN
     */
    public function api(string $token, string $npsn): DapodikClient
    {
        return new DapodikClient([
            'host' => $this->host,
            'port' => $this->port,
            'token' => $token,
            'npsn' => $npsn,
            'timeout' => $this->timeout,
        ]);
    }
}
