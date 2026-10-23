<?php

declare(strict_types=1);

namespace SixMm\Prediction\Tests;

use Google\Protobuf\Internal\Message;
use PHPUnit\Framework\TestCase;
use Prediction\V1\ListSymbolConfigsRequest;
use Prediction\V1\ListSymbolConfigsResponse;
use SixMm\Prediction\Admin\PredictionAdminTransport;
use SixMm\Prediction\Admin\PredictionCallContext;
use SixMm\Prediction\Admin\PredictionConfigRpcException;
use SixMm\Prediction\Admin\PredictionGrpcEndpoint;
use SixMm\Prediction\Protocol;

final class AdminSdkTest extends TestCase
{
    private function configuration(string $access = 'read_write'): array
    {
        return ['environment'=>'testing', 'grpc_address'=>'127.0.0.1:18081', 'timeout_ms'=>8000,
            'admin'=>['access'=>$access, 'token'=>'fixture-sdk-only-token-not-a-secret-0001']];
    }

    public function testEveryGatewayMethodUsesTheConfiguredSdkTransport(): void
    {
        $seen = [];
        $transport = new PredictionAdminTransport(configuration: $this->configuration(), invoker: static function ($service, $method, Message $request, $metadata, $options) use (&$seen): array {
            $copy = new ($request::class)();
            $copy->mergeFromString($request->serializeToString());
            self::assertSame($request->serializeToString(), $copy->serializeToString());
            self::assertSame(['42'], $metadata['x-operator-id']);
            self::assertSame(['sdk-test-trace'], $metadata['x-trace-id']);
            self::assertArrayNotHasKey('x-agent-id', $metadata);
            self::assertSame(8_000_000, $options['timeout']);
            $seen[] = [$service, $method, $request::class];
            return [new ListSymbolConfigsResponse(), (object) ['code'=>0]];
        });
        $config = new \SixMm\Prediction\Admin\GrpcPredictionConfigGateway($transport);
        $config->listConfigurations(null, '42', 'sdk-test-trace');
        $config->getConfiguration('UP_DOWN', 'BTCUSDT', '42', 'sdk-test-trace');
        $config->saveConfiguration(['game_type'=>'UP_DOWN', 'platform_template'=>[]], '42', 'sdk-test-trace');
        $groups = [
            'Operation' => ['listRounds','listOrders','getOrder'],
            'MarketMaker' => ['getSymbolConfiguration','saveSymbolConfiguration','getStrategy','saveStrategy','listRobotBindings','upsertRobotBinding','deleteRobotBinding','getReadiness','listRuntimeStatuses'],
            'Routing' => ['getSiteBinding','prepareSiteBindingChange','commitSiteBindingChange','cancelSiteBindingChange'],
            'PlatformRobot' => ['getPlatformRobot','listPlatformRobots','registerPlatformRobot','setPlatformRobotEnabled','getPlatformRobotBalances','createPlatformRobotOperation','getPlatformRobotOperation','listPlatformRobotOperations','createPlatformRobotUser','getPlatformRobotAutoDepositPolicy','savePlatformRobotAutoDepositPolicy'],
        ];
        foreach ($groups as $suffix=>$methods) {
            $class = 'SixMm\\Prediction\\Admin\\GrpcPrediction'.$suffix.'Gateway';
            $gateway = new $class($transport);
            foreach ($methods as $method) $gateway->{$method}([], '42', 'sdk-test-trace');
        }
        self::assertCount(30, $seen);
        self::assertSame('PredictionConfigAdmin', $seen[0][0]);
        self::assertSame(ListSymbolConfigsRequest::class, $seen[0][2]);
        self::assertSame(['PredictionRoutingAdmin','CommitSiteBindingChange'], array_slice($seen[17],0,2));
    }

    public function testMissingConfigurationFailsClosedWithoutFrameworkGlobals(): void
    {
        self::assertFalse(function_exists('config'));
        $this->expectException(PredictionConfigRpcException::class);
        (new PredictionAdminTransport())->assertAvailable();
    }

    public function testReadOnlyAndUnknownMethodsNeverReachInvoker(): void
    {
        $calls=0;
        $transport = new PredictionAdminTransport(configuration:$this->configuration('read_only'), invoker:static function () use (&$calls) { $calls++; });
        foreach ([['PredictionConfigAdmin','SaveSymbolConfig'],['PredictionConfigAdmin','Unknown']] as [$service,$method]) {
            try { $transport->invoke($service,$method,new ListSymbolConfigsRequest(),'42','trace'); self::fail('must reject'); }
            catch (PredictionConfigRpcException $e) { self::assertContains($e->errorCode(), ['ERROR_CODE_PERMISSION_DENIED','ERROR_CODE_INTERNAL']); }
        }
        self::assertSame(0,$calls);
    }

    public function testUnavailableCallHasOneAttemptAndSanitizedDiagnostic(): void
    {
        $calls=0;
        $transport = new PredictionAdminTransport(configuration:$this->configuration(),invoker:static function () use (&$calls) { $calls++; throw new \RuntimeException('private upstream diagnostic'); });
        try { $transport->invoke('PredictionConfigAdmin','ListSymbolConfigs',new ListSymbolConfigsRequest(),'42','trace'); self::fail('must reject'); }
        catch (PredictionConfigRpcException $e) { self::assertSame('ERROR_CODE_UNAVAILABLE',$e->errorCode()); self::assertStringNotContainsString('private',$e->getMessage()); }
        self::assertSame(1,$calls);
    }

    public function testEndpointAndTrustedIdentityValidationRemainStrict(): void
    {
        foreach (['','grpc://service:18081','dns:///service:18081','0.0.0.0:18081','[::]:18081','127.0.0.1:65536','user@service:18081'] as $endpoint) self::assertFalse(PredictionGrpcEndpoint::isValid($endpoint));
        foreach (['prediction.internal:18081','127.0.0.1:18081','[::1]:18081'] as $endpoint) self::assertTrue(PredictionGrpcEndpoint::isValid($endpoint));
        foreach (['0','-1','+1','1e3','9223372036854775808'] as $id) {
            try { new PredictionCallContext($id,'trace'); self::fail('invalid identity'); }
            catch (PredictionConfigRpcException $e) { self::assertSame(16,$e->grpcStatusCode()); }
        }
        self::assertSame('9223372036854775807',(new PredictionCallContext('9223372036854775807','trace'))->operatorId);
        self::assertFileExists(Protocol::directory().'/prediction/v1/market_maker.proto');
        self::assertStringContainsString('--grpc_out=',file_get_contents(Protocol::generationScript()));
    }
}
