<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin\Contracts;

interface PredictionMarketMakerGateway
{
    public function getSymbolConfiguration(array $payload, string $operatorId, string $traceId): array;
    public function saveSymbolConfiguration(array $payload, string $operatorId, string $traceId): array;
    public function getStrategy(array $payload, string $operatorId, string $traceId): array;
    public function saveStrategy(array $payload, string $operatorId, string $traceId): array;
    public function listRobotBindings(array $payload, string $operatorId, string $traceId): array;
    public function upsertRobotBinding(array $payload, string $operatorId, string $traceId): array;
    public function deleteRobotBinding(array $payload, string $operatorId, string $traceId): array;
    public function getReadiness(array $payload, string $operatorId, string $traceId): array;
    public function listRuntimeStatuses(array $payload, string $operatorId, string $traceId): array;
}
