<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

use SixMm\Prediction\Admin\Contracts\PredictionConfigGateway;

final class GrpcPredictionConfigGateway implements PredictionConfigGateway
{
    private PredictionAdminTransport $transport;
    private PredictionProtoMapper $mapper;
    public function __construct(PredictionAdminTransport $transport, ?PredictionProtoMapper $mapper = null)
    {
        $this->transport = $transport;
        $this->mapper = $mapper ?? new PredictionProtoMapper();
    }

    public function listConfigurations(?string $gameType, string $operatorId, string $requestId): array
    {
        return $this->call('ListSymbolConfigs', 'ListSymbolConfigsRequest', $gameType === null ? [] : ['game_type' => 'GAME_TYPE_' . $gameType], $operatorId, $requestId);
    }

    public function getConfiguration(string $gameType, string $symbol, string $operatorId, string $requestId): array
    {
        return $this->call('GetSymbolConfig', 'GetSymbolConfigRequest', [
            'game_type' => 'GAME_TYPE_' . $gameType,
            'symbol' => $symbol,
        ], $operatorId, $requestId);
    }

    public function saveConfiguration(array $payload, string $operatorId, string $requestId): array
    {
        if (!isset($payload['platform_template']) || isset($payload['agent_override']) || isset($payload['agent_id']) || isset($payload['based_on_agent_version'])) {
            throw new PredictionConfigRpcException(3, 'ERROR_CODE_INVALID_ARGUMENT', '平台配置请求只能提交 platform_template');
        }
        $payload['game_type'] = 'GAME_TYPE_' . $payload['game_type'];
        return $this->call('SaveSymbolConfig', 'SaveSymbolConfigRequest', $payload, $operatorId, $requestId);
    }

    private function call(string $method, string $request, array $payload, string $operator, string $trace): array
    {
        return $this->transport->invoke('PredictionConfigAdmin', $method, $this->mapper->request('Prediction\V1\\' . $request, $payload), $operator, $trace);
    }
}
