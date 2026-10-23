<?php

namespace SixMm\Prediction\Tests;

use SixMm\Prediction\Admin\PredictionProtoMapper;
use SixMm\Prediction\Admin\PredictionAdminTransport;
use SixMm\Prediction\Admin\PredictionConfigRpcException;
use PHPUnit\Framework\TestCase;

final class PredictionAdminClientTest extends TestCase
{
    public function testRoutingUnsignedMaximumSurvivesRealProtobufWireAndJson(): void
    {
        $m = new PredictionProtoMapper();
        $p = $m->request(\Prediction\V1\CommitSiteBindingChangeRequest::class, [
            'agent_id' => '42',
            'change_id' => 'change',
            'based_on_version' => '18446744073709551615',
            'client_request_id' => 'body',
            'reason' => '核验',
        ]);
        $copy = new \Prediction\V1\CommitSiteBindingChangeRequest();
        $copy->mergeFromString($p->serializeToString());
        self::assertSame('18446744073709551615', $m->messageToHttpArray($copy)['based_on_version']);
        $zero = $m->request(\Prediction\V1\PrepareSiteBindingChangeRequest::class, ['based_on_version' => '0']);
        self::assertSame('0', $m->messageToHttpArray($zero)['based_on_version']);
    }
    /** @dataProvider closedConfigurations */
    public function testNoCredentialFallbackOrInvalidEnvironmentCall(array $c): void
    {
        $calls = 0;
        $t = new PredictionAdminTransport(null, $c, function () use (&$calls) {
            $calls++;
            throw new \RuntimeException('Network must not be called');
        });
        try {
            $t->invoke('PredictionMarketMakerAdmin', 'GetStrategy', new \Prediction\V1\GetMarketMakerStrategyRequest(), '42', 'trace');
            self::fail('must reject');
        } catch (PredictionConfigRpcException $e) {
            self::assertSame(0, $calls);
        }
    }
    public static function closedConfigurations(): array
    {
        $c = [
            'environment' => 'testing',
            'grpc_address' => '127.0.0.1:18081',
            'shared_secret' => 'old-player-sentinel',
            'admin' => [
                'access' => 'read_write',
                'token' => '',
            ],
        ];
        $b = $c;
        $b['admin']['token'] = 'fixture-platform-admin-only-000001';
        $b['environment'] = 'unknown';
        $d = $b;
        $d['environment'] = 'testing';
        $d['grpc_address'] = 'grpc://company.internal:18081';
        return [[$c], [$b], [$d]];
    }

    public function testOptionalAbsentFalseEmptyAndUnsignedErrorPresence(): void
    {
        $m = new PredictionProtoMapper();
        $r = $m->request(\Prediction\V1\MarketMakerRuntimeBatch::class, [
            'attempt_no' => '18446744073709551615',
            'last_error' => '',
        ]);
        $a = $m->messageToHttpArray($r);
        self::assertSame('18446744073709551615', $a['attempt_no']);
        self::assertSame('', $a['last_error']);
        self::assertArrayNotHasKey('lease_until_ms', $a);
        $s = $m->request(\Prediction\V1\PlatformSymbolConfigInput::class, ['enabled' => false]);
        self::assertTrue($s->hasEnabled());
        self::assertFalse($m->messageToHttpArray($s)['enabled']);
    }
}
