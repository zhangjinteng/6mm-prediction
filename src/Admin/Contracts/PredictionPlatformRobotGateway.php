<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin\Contracts;

interface PredictionPlatformRobotGateway
{
    public function removePlatformRobot(array $payload, string $operatorId, string $traceId): array;
    public function getPlatformRobot(array $payload, string $operatorId, string $traceId): array;
    public function listPlatformRobots(array $payload, string $operatorId, string $traceId): array;
    public function registerPlatformRobot(array $payload, string $operatorId, string $traceId): array;
    public function setPlatformRobotEnabled(array $payload, string $operatorId, string $traceId): array;
    public function getPlatformRobotBalances(array $payload, string $operatorId, string $traceId): array;
    public function createPlatformRobotOperation(array $payload, string $operatorId, string $traceId): array;
    public function getPlatformRobotOperation(array $payload, string $operatorId, string $traceId): array;
    public function listPlatformRobotOperations(array $payload, string $operatorId, string $traceId): array;
    public function createPlatformRobotUser(array $payload, string $operatorId, string $traceId): array;
    public function getPlatformRobotAutoDepositPolicy(array $payload, string $operatorId, string $traceId): array;
    public function savePlatformRobotAutoDepositPolicy(array $payload, string $operatorId, string $traceId): array;

}
