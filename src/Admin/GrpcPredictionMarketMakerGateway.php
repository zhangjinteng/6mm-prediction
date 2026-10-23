<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class GrpcPredictionMarketMakerGateway implements Contracts\PredictionMarketMakerGateway
{
    private PredictionAdminTransport $transport;
    private PredictionProtoMapper $mapper;
    public function __construct(PredictionAdminTransport $transport, ?PredictionProtoMapper $mapper = null)
    {
        $this->transport = $transport;
        $this->mapper = $mapper ?? new PredictionProtoMapper();
    }

    public function getSymbolConfiguration(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'GetSymbolConfiguration', $this->mapper->request(\Prediction\V1\GetMarketMakerSymbolConfigurationRequest::class, $payload), $operatorId, $traceId);
    }

    public function saveSymbolConfiguration(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'SaveSymbolConfiguration', $this->mapper->request(\Prediction\V1\SaveMarketMakerSymbolConfigurationRequest::class, $payload), $operatorId, $traceId);
    }

    public function getStrategy(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'GetStrategy', $this->mapper->request(\Prediction\V1\GetMarketMakerStrategyRequest::class, $payload), $operatorId, $traceId);
    }

    public function saveStrategy(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'SaveStrategy', $this->mapper->request(\Prediction\V1\SaveMarketMakerStrategyRequest::class, $payload), $operatorId, $traceId);
    }

    public function listRobotBindings(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'ListRobotBindings', $this->mapper->request(\Prediction\V1\ListRobotBindingsRequest::class, $payload), $operatorId, $traceId);
    }

    public function upsertRobotBinding(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'UpsertRobotBinding', $this->mapper->request(\Prediction\V1\UpsertRobotBindingRequest::class, $payload), $operatorId, $traceId);
    }

    public function deleteRobotBinding(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'DeleteRobotBinding', $this->mapper->request(\Prediction\V1\DeleteRobotBindingRequest::class, $payload), $operatorId, $traceId);
    }

    public function getReadiness(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'GetReadiness', $this->mapper->request(\Prediction\V1\GetMarketMakerReadinessRequest::class, $payload), $operatorId, $traceId);
    }

    public function listRuntimeStatuses(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionMarketMakerAdmin', 'ListRuntimeStatuses', $this->mapper->request(\Prediction\V1\ListMarketMakerRuntimeStatusesRequest::class, $payload), $operatorId, $traceId);
    }
}
