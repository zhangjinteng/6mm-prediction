<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin\Contracts;

interface PredictionConfigGateway
{
    public function listConfigurations(?string $gameType, string $operatorId, string $requestId): array;

    public function getConfiguration(
        string $gameType,
        string $symbol,
        string $operatorId,
        string $requestId
    ): array;

    public function saveConfiguration(array $payload, string $operatorId, string $requestId): array;
}
