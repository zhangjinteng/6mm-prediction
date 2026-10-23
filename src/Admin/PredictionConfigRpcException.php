<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

use RuntimeException;
use Throwable;

final class PredictionConfigRpcException extends RuntimeException
{
    private int $grpcStatusCode;

    private string $errorCode;

    private array $detail;

    public function __construct(
        int $grpcStatusCode,
        string $errorCode,
        string $message,
        array $detail = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $grpcStatusCode, $previous);
        $this->grpcStatusCode = $grpcStatusCode;
        $this->errorCode = $errorCode;
        $this->detail = $detail;
    }

    public function grpcStatusCode(): int
    {
        return $this->grpcStatusCode;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function detail(): array
    {
        return $this->detail;
    }
}
