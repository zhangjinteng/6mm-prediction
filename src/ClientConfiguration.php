<?php

declare(strict_types=1);

namespace SixMm\Prediction;

use InvalidArgumentException;

final class ClientConfiguration
{
    public readonly string $target;
    public readonly string $token;
    public readonly int $timeoutMicroseconds;
    public readonly bool $tls;

    public function __construct(
        string $target = '127.0.0.1:18081',
        string $token = '',
        int $timeoutMicroseconds = 5000000,
        bool $tls = false
    ) {
        $this->target = trim($target);
        $this->token = trim($token);
        $this->timeoutMicroseconds = max($timeoutMicroseconds, 1);
        $this->tls = $tls;

        if ($this->target === '') {
            throw new InvalidArgumentException('Prediction gRPC target must not be empty.');
        }
    }
}
