<?php

namespace Smansage\Dapodik\Exceptions;

class DapodikAuthException extends DapodikException
{
    public function __construct(string $message = "Akses ditolak (401/403). Pastikan token dan IP client sudah didaftarkan di Pengaturan WebService Dapodik.")
    {
        parent::__construct($message, 403);
    }
}
