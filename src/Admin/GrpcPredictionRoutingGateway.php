<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class GrpcPredictionRoutingGateway implements Contracts\PredictionRoutingGateway
{
    private PredictionAdminTransport $transport;
    private PredictionProtoMapper $mapper;
    public function __construct(PredictionAdminTransport $transport, ?PredictionProtoMapper $mapper = null)
    {
        $this->transport = $transport;
        $this->mapper = $mapper ?? new PredictionProtoMapper();
    }

    public function getSiteBinding(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionRoutingAdmin', 'GetSiteBinding', $this->mapper->request(\Prediction\V1\GetSiteBindingRequest::class, $payload), $operatorId, $traceId);
    }

    public function prepareSiteBindingChange(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionRoutingAdmin', 'PrepareSiteBindingChange', $this->mapper->request(\Prediction\V1\PrepareSiteBindingChangeRequest::class, $payload), $operatorId, $traceId);
    }

    public function commitSiteBindingChange(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionRoutingAdmin', 'CommitSiteBindingChange', $this->mapper->request(\Prediction\V1\CommitSiteBindingChangeRequest::class, $payload), $operatorId, $traceId);
    }

    public function cancelSiteBindingChange(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionRoutingAdmin', 'CancelSiteBindingChange', $this->mapper->request(\Prediction\V1\CancelSiteBindingChangeRequest::class, $payload), $operatorId, $traceId);
    }
}
