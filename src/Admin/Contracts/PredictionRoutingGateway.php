<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin\Contracts;

interface PredictionRoutingGateway
{
    public function getSiteBinding(array $payload, string $operatorId, string $traceId): array;
    public function prepareSiteBindingChange(array $payload, string $operatorId, string $traceId): array;
    public function commitSiteBindingChange(array $payload, string $operatorId, string $traceId): array;
    public function cancelSiteBindingChange(array $payload, string $operatorId, string $traceId): array;
}
