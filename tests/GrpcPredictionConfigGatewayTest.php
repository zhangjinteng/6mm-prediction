<?php

declare(strict_types=1);

namespace SixMm\Prediction\Tests;

use SixMm\Prediction\Admin\GrpcPredictionConfigGateway;
use SixMm\Prediction\Admin\PredictionConfigRpcException;
use SixMm\Prediction\Admin\PredictionProtoMapper;
use Closure;
use Google\Protobuf\Any;
use Google\Protobuf\Internal\Message;
use Google\Rpc\Status as RichStatus;
use PHPUnit\Framework\TestCase;
use Prediction\V1\ErrorCode;
use Prediction\V1\GameType;
use Prediction\V1\GameplayConfig;
use Prediction\V1\ListSymbolConfigsRequest;
use Prediction\V1\ListSymbolConfigsResponse;
use Prediction\V1\MarkPriceUnavailableReason;
use Prediction\V1\PredictionErrorDetail;
use Prediction\V1\SaveSymbolConfigRequest;
use Prediction\V1\SaveSymbolConfigResponse;
use stdClass;

final class GrpcPredictionConfigGatewayTest extends TestCase
{
    public function testListSendsTrustedPlatformMetadataWithoutAgentIdentity(): void
    {
        $captured = [];
        $response = new ListSymbolConfigsResponse();
        $response->setConfigurations([
            new GameplayConfig([
                'template_version' => '9223372036854775807',
                'effective_version' => 'platform:8',
            ]),
        ]);
        $gateway = $this->gateway(function (
            string $method,
            Message $request,
            array $metadata,
            array $options
        ) use (&$captured, $response): array {
            $captured = compact('method', 'request', 'metadata', 'options');

            return [$response, $this->okStatus()];
        });

        $actual = $gateway->listConfigurations('UP_DOWN', '12', 'request-1');

        self::assertSame('ListSymbolConfigs', $captured['method']);
        self::assertInstanceOf(ListSymbolConfigsRequest::class, $captured['request']);
        self::assertSame(GameType::GAME_TYPE_UP_DOWN, $captured['request']->getGameType());
        self::assertSame(['Bearer fixture-platform-admin-only-000001'], $captured['metadata']['authorization']);
        self::assertSame(['12'], $captured['metadata']['x-operator-id']);
        self::assertSame(['request-1'], $captured['metadata']['x-trace-id']);
        self::assertArrayNotHasKey('x-agent-id', $captured['metadata']);
        self::assertSame(8000000, $captured['options']['timeout']);
        self::assertSame('9223372036854775807', $actual['configurations'][0]['template_version']);
    }

    public function testSaveBuildsOnlyPlatformTemplateAndPreservesVersionString(): void
    {
        $capturedRequest = null;
        $gateway = $this->gateway(function (
            string $method,
            Message $request,
            array $metadata,
            array $options
        ) use (&$capturedRequest): array {
            $capturedRequest = $request;

            return [new SaveSymbolConfigResponse(), $this->okStatus()];
        });

        $gateway->saveConfiguration([
            'client_request_id' => 'save-1',
            'reason' => '调整测试配置',
            'based_on_template_version' => '9223372036854775807',
            'game_type' => 'HIGH_LOW',
            'symbol' => 'BTCUSDT',
            'platform_template' => [
                'enabled' => false,
                'rules' => [],
            ],
        ], '12', 'request-2');

        self::assertInstanceOf(SaveSymbolConfigRequest::class, $capturedRequest);
        self::assertSame('9223372036854775807', (string) $capturedRequest->getBasedOnTemplateVersion());
        self::assertSame(GameType::GAME_TYPE_HIGH_LOW, $capturedRequest->getGameType());
        self::assertSame('platform_template', $capturedRequest->getConfiguration());
        self::assertFalse($capturedRequest->hasAgentOverride());
    }

    public function testItExposesStablePredictionErrorDetailFromGrpcTrailer(): void
    {
        $detail = new PredictionErrorDetail([
            'code' => ErrorCode::ERROR_CODE_MARK_PRICE_UNAVAILABLE,
            'message' => '行情已过期',
            'symbol' => 'BTCUSDT',
            'mark_price_unavailable_reason' => MarkPriceUnavailableReason::MARK_PRICE_UNAVAILABLE_REASON_STALE,
        ]);
        $any = new Any([
            'type_url' => 'type.googleapis.com/prediction.v1.PredictionErrorDetail',
            'value' => $detail->serializeToString(),
        ]);
        $richStatus = new RichStatus([
            'code' => 9,
            'message' => '行情已过期',
            'details' => [$any],
        ]);
        $status = $this->failedStatus(9, 'failed precondition', $richStatus->serializeToString());
        $gateway = $this->gateway(
            static fn (string $method, Message $request, array $metadata, array $options): array => [null, $status]
        );

        try {
            $gateway->listConfigurations(null, '12', 'request-3');
            self::fail('Expected PredictionConfigRpcException to be thrown.');
        } catch (PredictionConfigRpcException $exception) {
            self::assertSame(9, $exception->grpcStatusCode());
            self::assertSame('ERROR_CODE_MARK_PRICE_UNAVAILABLE', $exception->errorCode());
            self::assertSame('BTCUSDT', $exception->detail()['symbol']);
            self::assertSame(
                'MARK_PRICE_UNAVAILABLE_REASON_STALE',
                $exception->detail()['mark_price_unavailable_reason']
            );
        }
    }

    public function testItDoesNotExposeRawGrpcDetailsWithoutStructuredBusinessError(): void
    {
        $status = $this->failedStatus(13, 'sql connection failed: internal-host', '');
        $gateway = $this->gateway(
            static fn (string $method, Message $request, array $metadata, array $options): array => [null, $status]
        );

        try {
            $gateway->listConfigurations(null, '12', 'request-4');
            self::fail('Expected PredictionConfigRpcException to be thrown.');
        } catch (PredictionConfigRpcException $exception) {
            self::assertSame('ERROR_CODE_INTERNAL', $exception->errorCode());
            self::assertSame('Prediction gRPC 调用失败', $exception->getMessage());
            self::assertStringNotContainsString('internal-host', $exception->getMessage());
        }
    }

    public function testItDoesNotExposeStructuredInternalErrorDetail(): void
    {
        $detail = new PredictionErrorDetail([
            'code' => ErrorCode::ERROR_CODE_INTERNAL,
            'message' => 'sql connection failed: internal-host',
            'symbol' => 'internal-table',
        ]);
        $any = new Any([
            'type_url' => 'type.googleapis.com/prediction.v1.PredictionErrorDetail',
            'value' => $detail->serializeToString(),
        ]);
        $richStatus = new RichStatus([
            'code' => 13,
            'message' => 'sql connection failed: internal-host',
            'details' => [$any],
        ]);
        $status = $this->failedStatus(13, 'sql connection failed: internal-host', $richStatus->serializeToString());
        $gateway = $this->gateway(
            static fn (string $method, Message $request, array $metadata, array $options): array => [null, $status]
        );

        try {
            $gateway->listConfigurations(null, '12', 'request-5');
            self::fail('Expected PredictionConfigRpcException to be thrown.');
        } catch (PredictionConfigRpcException $exception) {
            self::assertSame('ERROR_CODE_INTERNAL', $exception->errorCode());
            self::assertSame('Prediction gRPC 调用失败', $exception->getMessage());
            self::assertSame([], $exception->detail());
        }
    }

    private function gateway(Closure $invoker): GrpcPredictionConfigGateway
    {
        return new GrpcPredictionConfigGateway(new \SixMm\Prediction\Admin\PredictionAdminTransport(
            new PredictionProtoMapper(),
            ['environment'=>'testing','grpc_address'=>'192.168.10.250:18081','timeout_ms'=>8000,'admin'=>['access'=>'read_write','token'=>'fixture-platform-admin-only-000001']],
            static fn($service,$method,$request,$metadata,$options)=>$invoker($method,$request,$metadata,$options)
        ));
    }

    private function okStatus(): stdClass
    {
        $status = new stdClass();
        $status->code = 0;
        $status->details = '';
        $status->metadata = [];

        return $status;
    }

    private function failedStatus(int $code, string $details, string $richDetails): stdClass
    {
        $status = new stdClass();
        $status->code = $code;
        $status->details = $details;
        $status->metadata = ['grpc-status-details-bin' => [$richDetails]];

        return $status;
    }
}
