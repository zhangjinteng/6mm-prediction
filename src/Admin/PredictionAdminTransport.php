<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

use Closure;
use Google\Protobuf\Internal\Message;
use Google\Rpc\Status as RichStatus;
use Grpc\ChannelCredentials;
use Prediction\V1\PredictionErrorDetail;
use Throwable;

final class PredictionAdminTransport
{
    private PredictionProtoMapper $mapper;
    private array $configuration;
    private array $clients = [];
    private ?\Grpc\Channel $channel = null;
    private ?Closure $invoker;
    private const METHODS = [
        'PredictionConfigAdmin' => ['ListSymbolConfigs', 'GetSymbolConfig', 'SaveSymbolConfig'],
        'PredictionAdminQuery' => ['ListRounds', 'ListOrders', 'GetOrder'],
        'PredictionMarketMakerAdmin' => [
            'GetSymbolConfiguration',
            'SaveSymbolConfiguration',
            'GetStrategy',
            'SaveStrategy',
            'ListRobotBindings',
            'UpsertRobotBinding',
            'DeleteRobotBinding',
            'GetReadiness',
            'ListRuntimeStatuses',
        ],
        'PredictionRoutingAdmin' => ['GetSiteBinding', 'PrepareSiteBindingChange', 'CommitSiteBindingChange', 'CancelSiteBindingChange'],
        'PredictionPlatformRobotAdmin' => ['RemovePlatformRobot', 'GetPlatformRobot', 'ListPlatformRobots', 'RegisterPlatformRobot', 'SetPlatformRobotEnabled', 'GetPlatformRobotBalances', 'CreatePlatformRobotOperation', 'GetPlatformRobotOperation', 'ListPlatformRobotOperations', 'CreatePlatformRobotUser', 'GetPlatformRobotAutoDepositPolicy', 'SavePlatformRobotAutoDepositPolicy'],
    ];
    private const WRITES = [
        'SaveSymbolConfiguration',
        'SaveSymbolConfig',
        'SaveStrategy',
        'UpsertRobotBinding',
        'DeleteRobotBinding',
        'PrepareSiteBindingChange',
        'CommitSiteBindingChange',
        'CancelSiteBindingChange',
        'RemovePlatformRobot',
        'RegisterPlatformRobot',
        'SetPlatformRobotEnabled',
        'CreatePlatformRobotOperation',
        'CreatePlatformRobotUser',
        'SavePlatformRobotAutoDepositPolicy',
    ];
    public function __construct(?PredictionProtoMapper $mapper = null, ?array $configuration = null, ?Closure $invoker = null)
    {
        $this->mapper = $mapper ?? new PredictionProtoMapper();
        $this->configuration = $configuration ?? [];
        $this->invoker = $invoker;
    }

    public function writesEnabled(): bool
    {
        try {
            $this->assertAvailable(true);
            return true;
        } catch (PredictionConfigRpcException $e) {
            return false;
        }
    }

    public function assertAvailable(bool $write = false): void
    {
        $c = $this->configuration;
        $admin = $c['admin'] ?? [];
        if (!in_array($admin['access'] ?? 'disabled', ['read_only', 'read_write'], true) || !in_array($c['environment'] ?? '', ['testing', 'local', 'production'], true)) {
            throw new PredictionConfigRpcException(14, 'ERROR_CODE_UNAVAILABLE', '预测管理服务暂未开放');
        }
        $secret = $admin['token'] ?? '';
        if (!is_string($secret) || strlen($secret) < 32 || $secret !== trim($secret) || preg_match('/[\r\n]/', $secret)) {
            throw new PredictionConfigRpcException(16, 'ERROR_CODE_UNAUTHENTICATED', 'Prediction 管理调用凭据未配置');
        }
        if (!PredictionGrpcEndpoint::isValid($c['grpc_address'] ?? null)) {
            throw new PredictionConfigRpcException(14, 'ERROR_CODE_UNAVAILABLE', 'Prediction 服务地址无效');
        }
        if ($write && $admin['access'] !== 'read_write') {
            throw new PredictionConfigRpcException(7, 'ERROR_CODE_PERMISSION_DENIED', '暂未开放站点或预测配置变更');
        }
    }

    public function invoke(string $service, string $method, Message $request, string $operatorId, string $traceId): array
    {
        if (!in_array($method, self::METHODS[$service] ?? [], true)) {
            throw new PredictionConfigRpcException(13, 'ERROR_CODE_INTERNAL', 'Prediction gRPC 调用失败');
        }
        $this->assertAvailable(in_array($method, self::WRITES, true));
        new PredictionCallContext($operatorId, $traceId);
        $metadata = [
            'authorization' => ['Bearer ' . $this->configuration['admin']['token']],
            'x-operator-id' => [$operatorId],
            'x-trace-id' => [$traceId],
        ];
        $options = ['timeout' => max(1, min(30000, (int) ($this->configuration['timeout_ms'] ?? 8000))) * 1000];
        try {
            if ($this->invoker) {
                [$response, $status] = ($this->invoker)($service, $method, $request, $metadata, $options);
            } else {
                if (!isset($this->clients[$service])) {
                    $class = 'Prediction\V1\\' . $service . 'Client';
                    $channelOptions = [
                        'credentials' => ChannelCredentials::createInsecure(),
                        'grpc.enable_retries' => 0,
                    ];
                    $this->channel ??= new \Grpc\Channel($this->configuration['grpc_address'], $channelOptions);
                    $this->clients[$service] = new $class($this->configuration['grpc_address'], $channelOptions, $this->channel);
                }
                [$response, $status] = $this->clients[$service]->{$method}($request, $metadata, $options)->wait();
            }
        } catch (PredictionConfigRpcException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new PredictionConfigRpcException(14, 'ERROR_CODE_UNAVAILABLE', 'Prediction gRPC 服务暂不可用', [], $e);
        }
        $code = $this->statusCode($status);
        if ($code !== 0) {
            throw $this->exceptionFromStatus($code, $status);
        }
        if (!$response instanceof Message) {
            throw new PredictionConfigRpcException(13, 'ERROR_CODE_INTERNAL', 'Prediction gRPC 返回了无效响应');
        }
        return $this->mapper->messageToHttpArray($response);
    }

    public function __destruct()
    {
        if ($this->channel !== null) {
            $this->channel->close();
        }
    }

    private function statusCode($status): int
    {
        if (is_object($status) && isset($status->code)) {
            return (int) $status->code;
        }
        if (is_array($status) && isset($status['code'])) {
            return (int) $status['code'];
        }
        return 2;
    }

    private function exceptionFromStatus(int $statusCode, $status): PredictionConfigRpcException
    {
        $detail = $this->predictionErrorDetail($status);
        $detailErrorCode = (string) ($detail['code'] ?? '');
        $errorCode = $detailErrorCode !== '' && $detailErrorCode !== 'ERROR_CODE_UNSPECIFIED' ? $detailErrorCode : $this->fallbackErrorCode($statusCode);
        $isPublicBusinessError = $this->isPublicBusinessError($errorCode);
        $structuredMessage = trim((string) ($detail['message'] ?? ''));
        $message = $structuredMessage !== '' && $isPublicBusinessError ? $structuredMessage : $this->fallbackMessage($errorCode);
        return new PredictionConfigRpcException($statusCode, $errorCode, $message, $isPublicBusinessError ? $detail : []);
    }

    private function predictionErrorDetail($status): array
    {
        $metadata = is_object($status) && isset($status->metadata) ? $status->metadata : (is_array($status) ? $status['metadata'] ?? [] : []);
        $binaryValues = is_array($metadata) ? $metadata['grpc-status-details-bin'] ?? [] : [];
        $binary = is_array($binaryValues) ? $binaryValues[0] ?? '' : $binaryValues;
        if (!is_string($binary) || $binary === '') {
            return [];
        }
        try {
            $richStatus = new RichStatus();
            $richStatus->mergeFromString($binary);
            foreach ($richStatus->getDetails() as $any) {
                if (!str_ends_with($any->getTypeUrl(), '/prediction.v1.PredictionErrorDetail')) {
                    continue;
                }
                $detail = new PredictionErrorDetail();
                $detail->mergeFromString($any->getValue());
                return $this->mapper->messageToHttpArray($detail);
            }
        } catch (Throwable $exception) {
            return [];
        }
        return [];
    }

    private function fallbackErrorCode(int $statusCode): string
    {
        return match ($statusCode) {
            3 => 'ERROR_CODE_INVALID_ARGUMENT',
            5 => 'ERROR_CODE_NOT_FOUND',
            7 => 'ERROR_CODE_PERMISSION_DENIED',
            9 => 'ERROR_CODE_CONFLICT',
            10 => 'ERROR_CODE_OUTCOME_UNKNOWN',
            16 => 'ERROR_CODE_UNAUTHENTICATED',
            1 => 'ERROR_CODE_CANCELED',
            4, 14 => 'ERROR_CODE_UNAVAILABLE',
            default => 'ERROR_CODE_INTERNAL',
        };
    }

    private function isPublicBusinessError(string $errorCode): bool
    {
        return in_array($errorCode, [
            'ERROR_CODE_INVALID_ARGUMENT',
            'ERROR_CODE_NOT_FOUND',
            'ERROR_CODE_CONFLICT',
            'ERROR_CODE_OUTCOME_UNKNOWN',
            'ERROR_CODE_MARK_PRICE_UNAVAILABLE',
            'ERROR_CODE_UNAUTHENTICATED',
            'ERROR_CODE_PERMISSION_DENIED',
            'ERROR_CODE_UNAVAILABLE',
            'ERROR_CODE_CANCELED',
        ], true);
    }

    private function fallbackMessage(string $errorCode): string
    {
        return match ($errorCode) {
            'ERROR_CODE_INVALID_ARGUMENT' => '预测配置参数不合法',
            'ERROR_CODE_NOT_FOUND' => '预测配置不存在',
            'ERROR_CODE_CONFLICT' => '预测配置或目标版本冲突',
            'ERROR_CODE_OUTCOME_UNKNOWN' => '操作结果尚未确定，请保留原请求号核验',
            'ERROR_CODE_MARK_PRICE_UNAVAILABLE' => '预测行情暂不可用',
            'ERROR_CODE_UNAUTHENTICATED' => 'Prediction gRPC 调用身份无效',
            'ERROR_CODE_PERMISSION_DENIED' => 'Prediction gRPC 调用权限不足',
            'ERROR_CODE_UNAVAILABLE', 'ERROR_CODE_CANCELED' => 'Prediction gRPC 服务暂不可用',
            default => 'Prediction gRPC 调用失败',
        };
    }
}
