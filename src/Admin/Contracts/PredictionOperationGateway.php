<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin\Contracts;

interface PredictionOperationGateway
{
    public function listRounds(array $payload, string $operatorId, string $traceId): array;
    public function listOrders(array $payload, string $operatorId, string $traceId): array;
    public function getOrder(array $payload, string $operatorId, string $traceId): array;
}
