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

    public function removePlatformRobot(array $payload, string $operatorId, string $traceId): array
    {
        $response = $this->transport->invoke('PredictionPlatformRobotAdmin', 'RemovePlatformRobot',
            $this->mapper->request(\Prediction\V1\RemovePlatformRobotRequest::class, $payload), $operatorId, $traceId);
        $receipt = $response['receipt'] ?? [];
        if (($receipt['operation_id'] ?? '') === '' ||
            ($receipt['operator_id'] ?? null) !== $operatorId ||
            ($receipt['client_request_id'] ?? null) !== ($payload['client_request_id'] ?? null) ||
            ($receipt['source_service'] ?? null) !== 'platform_admin' ||
            ($receipt['action'] ?? null) !== 'ADMIN_ACTION_PLATFORM_ROBOT_REMOVE' ||
            ($receipt['target_type'] ?? null) !== 'PLATFORM_ROBOT' ||
            ($receipt['target_id'] ?? null) !== ($payload['user_id'] ?? null) ||
            ($response['user_id'] ?? null) !== ($payload['user_id'] ?? null)) {
            throw new PredictionConfigRpcException(13, 'ERROR_CODE_INTERNAL', '机器人移除回执不匹配，请保留原请求核验');
        }
        if (($receipt['result'] ?? '') === 'OPERATION_RESULT_FAILED' && ($response['removed'] ?? null) === false) {
            $failure = match ($receipt['error_code'] ?? '') {
                'PLATFORM_ROBOT_VERSION_CONFLICT', 'PLATFORM_ROBOT_VERSION_OVERFLOW' =>
                    [10, 'ERROR_CODE_CONFLICT', '机器人状态已变化，请刷新列表后重试'],
                'PLATFORM_ROBOT_NOT_FOUND' => [5, 'ERROR_CODE_NOT_FOUND', '机器人不存在或未完成注册'],
                'PLATFORM_ROBOT_INVALID' => [3, 'ERROR_CODE_INVALID_ARGUMENT', '机器人移除参数无效'],
                default => null,
            };
            if ($failure !== null) {
                throw new PredictionConfigRpcException($failure[0], $failure[1], $failure[2], [
                    'client_request_id' => $receipt['client_request_id'],
                    'control' => ['submission_outcome' => 'REJECTED'],
                ]);
            }
        }
        if (($receipt['result'] ?? '') !== 'OPERATION_RESULT_SUCCEEDED' || ($response['removed'] ?? null) !== true) {
            throw new PredictionConfigRpcException(13, 'ERROR_CODE_INTERNAL', '机器人移除结果尚未确认，请保留原请求核验');
        }
        return $response;
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
