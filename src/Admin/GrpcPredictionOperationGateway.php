<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class GrpcPredictionOperationGateway implements Contracts\PredictionOperationGateway
{
    private PredictionAdminTransport $transport;
    private PredictionProtoMapper $mapper;
    public function __construct(PredictionAdminTransport $transport, ?PredictionProtoMapper $mapper = null)
    {
        $this->transport = $transport;
        $this->mapper = $mapper ?? new PredictionProtoMapper();
    }

    public function listRounds(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionAdminQuery', 'ListRounds', $this->mapper->request(\Prediction\V1\ListAdminRoundsRequest::class, $this->enums($payload, true)), $operatorId, $traceId);
    }

    public function listOrders(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionAdminQuery', 'ListOrders', $this->mapper->request(\Prediction\V1\ListAdminOrdersRequest::class, $this->enums($payload, false)), $operatorId, $traceId);
    }

    public function getOrder(array $payload, string $operatorId, string $traceId): array
    {
        return $this->transport->invoke('PredictionAdminQuery', 'GetOrder', $this->mapper->request(\Prediction\V1\GetAdminOrderRequest::class, $payload), $operatorId, $traceId);
    }

    private function enums(array $payload, bool $round): array
    {
        $prefixes = [
            'game_type' => 'GAME_TYPE_',
            'time_field' => 'ADMIN_QUERY_TIME_FIELD_',
            'direction' => 'DIRECTION_',
            'order_source' => 'ORDER_SOURCE_',
            'view' => 'ADMIN_ORDER_VIEW_',
            'business_status' => 'BUSINESS_STATUS_',
            'fund_status' => 'FUND_STATUS_',
            'result' => 'ORDER_RESULT_',
            'status' => 'ROUND_STATE_',
            'sort_direction' => 'ADMIN_SORT_DIRECTION_',
            'sort_field' => $round ? 'ADMIN_ROUND_SORT_FIELD_' : 'ADMIN_ORDER_SORT_FIELD_',
        ];
        foreach ($prefixes as $field => $prefix) {
            if (isset($payload[$field])) {
                $payload[$field] = $prefix . $payload[$field];
            }
        }
        return $payload;
    }
}
