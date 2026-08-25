<?php

declare(strict_types=1);

namespace SixMm\Prediction\Exceptions;

use RuntimeException;
use Throwable;

final class PredictionRpcException extends RuntimeException
{
    public function __construct(
        private readonly string $rpcMethod,
        private readonly int $statusCode,
        private readonly string $statusDetails,
        ?Throwable $previous = null
    ) {
        parent::__construct(sprintf(
            'Prediction RPC %s failed with status %d: %s',
            $rpcMethod,
            $statusCode,
            $statusDetails !== '' ? $statusDetails : 'unknown error'
        ), $statusCode, $previous);
    }

    public function rpcMethod(): string
    {
        return $this->rpcMethod;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function statusDetails(): string
    {
        return $this->statusDetails;
    }
}
