<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class GrpcPredictionPlatformRobotGateway implements Contracts\PredictionPlatformRobotGateway
{
    private PredictionAdminTransport $transport;
    private PredictionProtoMapper $mapper;
    public function __construct(PredictionAdminTransport $transport, ?PredictionProtoMapper $mapper = null)
    {
        $this->transport = $transport;
        $this->mapper = $mapper ?? new PredictionProtoMapper();
    }

    public function getPlatformRobot(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'GetPlatformRobot', $this->mapper->request(\Prediction\V1\GetPlatformRobotRequest::class, $payload), $operatorId, $traceId);
    }

    public function listPlatformRobots(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'ListPlatformRobots', $this->mapper->request(\Prediction\V1\ListPlatformRobotsRequest::class, $payload), $operatorId, $traceId);
    }

    public function registerPlatformRobot(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'RegisterPlatformRobot', $this->mapper->request(\Prediction\V1\RegisterPlatformRobotRequest::class, $payload), $operatorId, $traceId);
    }

    public function setPlatformRobotEnabled(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'SetPlatformRobotEnabled', $this->mapper->request(\Prediction\V1\SetPlatformRobotEnabledRequest::class, $payload), $operatorId, $traceId);
    }

    public function getPlatformRobotBalances(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'GetPlatformRobotBalances', $this->mapper->request(\Prediction\V1\GetPlatformRobotBalancesRequest::class, $payload), $operatorId, $traceId);
    }

    public function createPlatformRobotOperation(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'CreatePlatformRobotOperation', $this->mapper->request(\Prediction\V1\CreatePlatformRobotOperationRequest::class, $payload), $operatorId, $traceId);
    }

    public function getPlatformRobotOperation(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'GetPlatformRobotOperation', $this->mapper->request(\Prediction\V1\GetPlatformRobotOperationRequest::class, $payload), $operatorId, $traceId);
    }

    public function listPlatformRobotOperations(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'ListPlatformRobotOperations', $this->mapper->request(\Prediction\V1\ListPlatformRobotOperationsRequest::class, $payload), $operatorId, $traceId);
    }

    public function createPlatformRobotUser(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'CreatePlatformRobotUser', $this->mapper->request(\Prediction\V1\CreatePlatformRobotUserRequest::class, $payload), $operatorId, $traceId);
    }
    public function getPlatformRobotAutoDepositPolicy(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'GetPlatformRobotAutoDepositPolicy', $this->mapper->request(\Prediction\V1\GetPlatformRobotAutoDepositPolicyRequest::class, $payload), $operatorId, $traceId);
    }
    public function savePlatformRobotAutoDepositPolicy(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionPlatformRobotAdmin', 'SavePlatformRobotAutoDepositPolicy', $this->mapper->request(\Prediction\V1\SavePlatformRobotAutoDepositPolicyRequest::class, $payload), $operatorId, $traceId);
    }

}
