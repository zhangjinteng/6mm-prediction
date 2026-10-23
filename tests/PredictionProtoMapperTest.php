<?php

declare(strict_types=1);

namespace SixMm\Prediction\Tests;

use SixMm\Prediction\Admin\PredictionProtoMapper;
use PHPUnit\Framework\TestCase;
use Prediction\V1\DecimalConfigField;
use Prediction\V1\GameplayConfigRule;
use Prediction\V1\GameplayStakeTier;
use Prediction\V1\GameplayConfig;
use Prediction\V1\ListSymbolConfigsResponse;

final class PredictionProtoMapperTest extends TestCase
{
    public function testPriceDecimalsOptionalZeroRoundTrip(): void
    {
        $mapper=new PredictionProtoMapper();
        foreach ([null,0,8] as $n) {
            $payload=$n===null?[]:['price_decimals'=>$n];
            $rule=$mapper->request(\Prediction\V1\PriceRule::class,$payload);
            $result=$mapper->messageToHttpArray($rule);
            if ($n===null) {self::assertArrayNotHasKey('price_decimals',$result);} else {self::assertSame($n,$result['price_decimals']);}
        }
    }

    public function testItConvertsNestedHttpKeysWithoutChangingPreciseValues(): void
    {
        $mapper = new PredictionProtoMapper();

        $actual = $mapper->toProtoJson([
            'based_on_template_version' => '9223372036854775807',
            'platform_template' => [
                'rules' => [[
                    'fixed_odds' => ['default_value' => '1.80'],
                    'enabled_by_default' => true,
                ]],
            ],
        ]);

        self::assertSame([
            'basedOnTemplateVersion' => '9223372036854775807',
            'platformTemplate' => [
                'rules' => [[
                    'fixedOdds' => ['defaultValue' => '1.80'],
                    'enabledByDefault' => true,
                ]],
            ],
        ], $actual);
    }

    public function testItSerializesProtoInt64ValuesAsHttpStrings(): void
    {
        $configuration = new GameplayConfig();
        $configuration->setTemplateVersion('9223372036854775807');
        $configuration->setEffectiveVersion('version-8');

        $actual = (new PredictionProtoMapper())->messageToHttpArray($configuration);

        self::assertSame('9223372036854775807', $actual['template_version']);
        self::assertSame('version-8', $actual['effective_version']);
    }

    public function testItKeepsProtoDefaultScalarsAndEmptyRepeatedFieldsForRoundTrip(): void
    {
        $minimumStake = new DecimalConfigField();
        $minimumStake->setDefaultValue('1.00');

        $tier = new GameplayStakeTier();
        $tier->setStakeTierId('tier-1');
        $tier->setMinimumStake($minimumStake);

        $rule = new GameplayConfigRule();
        $rule->setSymbol('BTCUSDT');
        $rule->setMinimumStake($minimumStake);
        $rule->setStakeTiers([$tier]);

        $configuration = new GameplayConfig();
        $configuration->setRules([$rule]);

        $response = new ListSymbolConfigsResponse();
        $response->setConfigurations([$configuration]);

        $actual = (new PredictionProtoMapper())->messageToHttpArray($response);
        $actualRule = $actual['configurations'][0]['rules'][0];
        $actualTier = $actualRule['stake_tiers'][0];

        self::assertFalse($actualRule['enabled']);
        self::assertFalse($actualRule['enabled_by_default']);
        self::assertSame([], $actualRule['forced_adjustments']);
        self::assertSame('0', $actualRule['duration_seconds']);
        self::assertFalse($actualRule['minimum_stake']['agent_editable']);
        self::assertSame(0, $actualTier['display_order']);
        self::assertFalse($actualTier['retired']);
    }
    public function testSymbolMasterPresenceDistinguishesLegacyAndExplicitOff(): void
    {
        $mapper = new PredictionProtoMapper(); $config = new GameplayConfig();
        self::assertArrayNotHasKey('symbol_enabled', $mapper->messageToHttpArray($config));
        $config->setSymbolEnabled(false);
        self::assertFalse($mapper->messageToHttpArray($config)['symbol_enabled']);
        $config->setSymbolEnabled(true);
        self::assertTrue($mapper->messageToHttpArray($config)['symbol_enabled']);
    }

    public function testAtomicMakerCoinProtoPreservesEmptyRoomsAndVersions(): void
    {
        $mapper = new PredictionProtoMapper();
        $body=['client_request_id'=>'symbol-1','reason'=>'币种总关','symbol'=>'BTCUSDT','based_on_version'=>'9223372036854775806','enabled'=>false,'rooms'=>[]];
        $request=$mapper->request(\Prediction\V1\SaveMarketMakerSymbolConfigurationRequest::class,$body);
        self::assertSame($body['based_on_version'], $mapper->messageToHttpArray($request)['based_on_version']);
        self::assertSame([], $mapper->messageToHttpArray($request)['rooms']);
        self::assertFalse($mapper->messageToHttpArray($request)['enabled']);
        self::assertTrue(method_exists(\Prediction\V1\PredictionMarketMakerAdminClient::class,'SaveSymbolConfiguration'));
    }
}
