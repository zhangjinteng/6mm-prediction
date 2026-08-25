<?php

declare(strict_types=1);

use Prediction\V1\AdminOrderPage;
use Prediction\V1\ConfigScope;
use Prediction\V1\ConfigValidationStatus;
use Prediction\V1\DecimalConfigField;
use Prediction\V1\DecimalConstraint;
use Prediction\V1\GameType;
use Prediction\V1\GameplayConfig;
use Prediction\V1\GameplayConfigRule;
use Prediction\V1\GetGameplayConfigRequest;
use Prediction\V1\GetGameplayConfigResponse;
use Prediction\V1\IntegerConfigField;
use Prediction\V1\IntegerConstraint;
use Prediction\V1\ListAdminOrdersRequest;
use Prediction\V1\OperationReceipt;
use Prediction\V1\PageExtend;
use Prediction\V1\PlatformRuleTemplate;
use Prediction\V1\PlatformSymbolConfigInput;
use Prediction\V1\PlatformTemplateDraft;
use Prediction\V1\PlatformTemplateVersion;
use Prediction\V1\PredictionAdminClient;
use Prediction\V1\PredictionConfigAdminClient;
use Prediction\V1\PriceRule;
use Prediction\V1\PublishPlatformTemplateResponse;
use Prediction\V1\SaveMerchantSymbolConfigRequest;
use Prediction\V1\SaveSymbolConfigRequest;
use Prediction\V1\SavePlatformTemplateDraftResponse;
use Prediction\V1\SavePlatformSymbolConfigResponse;
use Prediction\V1\TemplateReconciliationJob;
use SixMm\Prediction\ClientConfiguration;
use SixMm\Prediction\Exceptions\PredictionRpcException;
use SixMm\Prediction\PlatformTemplates\PlatformTemplateClient;

require dirname(__DIR__) . '/vendor/autoload.php';

$assertions = 0;

$assertSame = static function (mixed $expected, mixed $actual, string $message) use (&$assertions): void {
    $assertions++;
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf(
            "%s\nExpected: %s\nActual:   %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
};

$assertTrue = static function (bool $condition, string $message) use (&$assertions): void {
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$decimal = static fn (string $default, string $minimum, string $maximum): DecimalConstraint =>
    new DecimalConstraint([
        'default_value' => $default,
        'minimum_value' => $minimum,
        'maximum_value' => $maximum,
        'merchant_editable' => true,
    ]);

$upDownRule = static fn (): PlatformRuleTemplate => (new PlatformRuleTemplate())
    ->setGameType(GameType::GAME_TYPE_UP_DOWN)
    ->setSymbol('BTCUSDT')
    ->setDurationSeconds(60)
    ->setEnabledByDefault(true)
    ->setBetOpenSeconds(new IntegerConstraint([
        'default_value' => 45,
        'minimum_value' => 10,
        'maximum_value' => 50,
        'merchant_editable' => true,
    ]))
    ->setTargetPayoutRate($decimal('0.9', '0.7', '0.95'))
    ->setMinimumOdds($decimal('1.2', '1', '2'))
    ->setMaximumOdds($decimal('5', '2', '10'))
    ->setMinimumStake($decimal('1', '0.1', '100'))
    ->setMaximumStake($decimal('1000', '100', '100000'))
    ->setSettlementDisplaySeconds(10)
    ->setPriceRule(new PriceRule([
        'max_age_milliseconds' => 5000,
        'late_arrival_grace_milliseconds' => 500,
    ]));

$highLowRule = static fn (): PlatformRuleTemplate => (new PlatformRuleTemplate())
    ->setGameType(GameType::GAME_TYPE_HIGH_LOW)
    ->setSymbol('ETHUSDT')
    ->setDurationSeconds(180)
    ->setEnabledByDefault(false)
    ->setFixedOdds($decimal('1.8', '1.1', '3'))
    ->setMinimumStake($decimal('1', '0.1', '100'))
    ->setMaximumStake($decimal('1000', '100', '100000'))
    ->setSettlementDisplaySeconds(10)
    ->setPriceRule(new PriceRule([
        'max_age_milliseconds' => 5000,
        'late_arrival_grace_milliseconds' => 500,
    ]));

$decimalData = static fn (string $default, string $minimum, string $maximum): array => [
    'default_value' => $default,
    'minimum_value' => $minimum,
    'maximum_value' => $maximum,
    'merchant_editable' => true,
];

$upDownData = static fn (): array => [
    'game_type' => 'UP_DOWN',
    'symbol' => 'BTCUSDT',
    'duration_seconds' => 60,
    'enabled_by_default' => true,
    'bet_open_seconds' => [
        'default_value' => 45,
        'minimum_value' => 10,
        'maximum_value' => 50,
        'merchant_editable' => true,
    ],
    'target_payout_rate' => $decimalData('0.9', '0.7', '0.95'),
    'minimum_odds' => $decimalData('1.2', '1', '2'),
    'maximum_odds' => $decimalData('5', '2', '10'),
    'fixed_odds' => null,
    'minimum_stake' => $decimalData('1', '0.1', '100'),
    'maximum_stake' => $decimalData('1000', '100', '100000'),
    'settlement_display_seconds' => 10,
    'price_rule' => [
        'max_age_milliseconds' => 5000,
        'late_arrival_grace_milliseconds' => 500,
    ],
];

$highLowData = static fn (): array => [
    'game_type' => 'HIGH_LOW',
    'symbol' => 'ETHUSDT',
    'duration_seconds' => 180,
    'enabled_by_default' => false,
    'bet_open_seconds' => null,
    'target_payout_rate' => null,
    'minimum_odds' => null,
    'maximum_odds' => null,
    'fixed_odds' => $decimalData('1.8', '1.1', '3'),
    'minimum_stake' => $decimalData('1', '0.1', '100'),
    'maximum_stake' => $decimalData('1000', '100', '100000'),
    'settlement_display_seconds' => 10,
    'price_rule' => [
        'max_age_milliseconds' => 5000,
        'late_arrival_grace_milliseconds' => 500,
    ],
];

$configuration = new ClientConfiguration('127.0.0.1:18081', 'test-token', 1000000, false);

$capturedSymbolRequest = null;
$saveSymbolClient = new PlatformTemplateClient(
    $configuration,
    static function (string $method, object $request) use (&$capturedSymbolRequest, $upDownRule): array {
        $capturedSymbolRequest = $request;

        return [
            (new SavePlatformSymbolConfigResponse())
                ->setVersion((new PlatformTemplateVersion())
                    ->setVersionId('version-8')
                    ->setVersion(8)
                    ->setRules([$upDownRule()]))
                ->setReceipt((new OperationReceipt())
                    ->setOperationId('operation-8')
                    ->setClientRequestId('save-symbol-request-1')),
            (object) ['code' => 0, 'details' => ''],
        ];
    }
);
$savedSymbol = $saveSymbolClient->saveSymbolConfig('operator-1', [
    'client_request_id' => 'save-symbol-request-1',
    'reason' => 'save BTCUSDT prediction configuration',
    'based_on_version' => 7,
    'game_type' => 'UP_DOWN',
    'symbol' => 'BTCUSDT',
    'platform_enabled' => true,
    'rules' => [$upDownData()],
]);
$assertSame(1, $capturedSymbolRequest->getSchemaVersion(), 'Symbol saves should set the schema version.');
$assertSame(7, $capturedSymbolRequest->getBasedOnVersion(), 'Symbol saves should preserve the base version.');
$assertSame(GameType::GAME_TYPE_UP_DOWN, $capturedSymbolRequest->getGameType(), 'Game type should be mapped.');
$assertSame('BTCUSDT', $capturedSymbolRequest->getSymbol(), 'Symbol saves should preserve the symbol.');
$assertTrue($capturedSymbolRequest->hasPlatformEnabled(), 'Symbol saves should include the platform switch.');
$assertTrue($capturedSymbolRequest->getPlatformEnabled(), 'The platform switch should preserve its value.');
$assertSame(1, count($capturedSymbolRequest->getRules()), 'Symbol saves should include every supplied duration.');
$assertSame(8, $savedSymbol['version']['version'], 'The directly published version should be mapped.');
$assertSame('operation-8', $savedSymbol['receipt']['operation_id'], 'The direct-save receipt should be mapped.');

$merchantSymbolRequest = (new SaveMerchantSymbolConfigRequest())->setMerchantEnabled(false);
$assertTrue(
    $merchantSymbolRequest->hasMerchantEnabled(),
    'Generated merchant symbol requests should preserve optional switch presence.'
);
$unifiedGameplayRequest = (new GetGameplayConfigRequest())
    ->setGameType(GameType::GAME_TYPE_UP_DOWN)
    ->setSymbol('BTCUSDT');
$assertSame(GameType::GAME_TYPE_UP_DOWN, $unifiedGameplayRequest->getGameType(), 'Unified gameplay queries should set game type.');
$assertSame('BTCUSDT', $unifiedGameplayRequest->getSymbol(), 'Unified gameplay queries should set symbol.');
$unifiedSaveRequest = (new SaveSymbolConfigRequest())
    ->setSchemaVersion(1)
    ->setClientRequestId('unified-save-request-1')
    ->setReason('package unified save test')
    ->setBasedOnTemplateVersion(8)
    ->setBasedOnMerchantVersion(0)
    ->setGameType(GameType::GAME_TYPE_UP_DOWN)
    ->setSymbol('BTCUSDT')
    ->setPlatformTemplate((new PlatformSymbolConfigInput())
        ->setEnabled(true)
        ->setRules([$upDownRule()]));
$assertTrue($unifiedSaveRequest->hasPlatformTemplate(), 'Unified saves should support platform-template oneof input.');
$assertTrue($unifiedSaveRequest->getPlatformTemplate()->hasEnabled(), 'Unified platform saves should preserve optional enabled presence.');
$assertSame(1, count($unifiedSaveRequest->getPlatformTemplate()->getRules()), 'Unified platform saves should carry symbol rules.');
$configurationView = (new GameplayConfig())
    ->setScope(ConfigScope::CONFIG_SCOPE_PLATFORM)
    ->setTemplateVersion(8)
    ->setMerchantVersion(0)
    ->setEffectiveVersion('8:0');
$assertSame(ConfigScope::CONFIG_SCOPE_PLATFORM, $configurationView->getScope(), 'Unified config should expose the response scope.');
$assertSame('8:0', $configurationView->getEffectiveVersion(), 'Unified config should expose the effective version.');
$flatGameplayRule = (new GameplayConfigRule())
    ->setGameType(GameType::GAME_TYPE_HIGH_LOW)
    ->setSymbol('BTCUSDT')
    ->setDurationSeconds(60)
    ->setEnabled(true)
    ->setEnabledByDefault(true)
    ->setValidationStatus(ConfigValidationStatus::CONFIG_VALIDATION_STATUS_VALID)
    ->setBetOpenSeconds((new IntegerConfigField())
        ->setValue(45)
        ->setTemplateValue(50)
        ->setDefaultValue(50)
        ->setMinimumValue(10)
        ->setMaximumValue(60)
        ->setMerchantEditable(true)
        ->setOverridden(true))
    ->setFixedOdds((new DecimalConfigField())
        ->setValue('1.70')
        ->setTemplateValue('1.80')
        ->setDefaultValue('1.80')
        ->setMinimumValue('1.10')
        ->setMaximumValue('3.00')
        ->setMerchantEditable(true)
        ->setOverridden(true))
    ->setMinimumStake((new DecimalConfigField())
        ->setValue('1.00')
        ->setTemplateValue('1.00')
        ->setDefaultValue('1.00')
        ->setMinimumValue('0.10')
        ->setMaximumValue('100.00')
        ->setMerchantEditable(true)
        ->setOverridden(false))
    ->setSettlementDisplaySeconds(5)
    ->setPriceRule((new PriceRule())
        ->setMaxAgeMilliseconds(3000)
        ->setLateArrivalGraceMilliseconds(500));
$configurationView->setRules([$flatGameplayRule]);
$assertSame('BTCUSDT', $configurationView->getRules()[0]->getSymbol(), 'Unified config rules should expose the flat symbol field.');
$assertSame(60, $configurationView->getRules()[0]->getDurationSeconds(), 'Unified config rules should expose the flat duration field.');
$assertSame(50, $configurationView->getRules()[0]->getBetOpenSeconds()->getTemplateValue(), 'Integer config fields should expose template value.');
$assertTrue($configurationView->getRules()[0]->getBetOpenSeconds()->getOverridden(), 'Integer config fields should expose override state.');
$assertSame('1.80', $configurationView->getRules()[0]->getFixedOdds()->getTemplateValue(), 'Decimal config fields should expose template value.');
$assertTrue(!$configurationView->getRules()[0]->getMinimumStake()->getOverridden(), 'Decimal config fields should expose inherited state.');
$getClient = new PlatformTemplateClient(
    $configuration,
    static function (string $method, object $request, string $operatorId, string $capability) use (
        $configurationView,
        $assertSame
    ): array {
        $assertSame('GetGameplayConfig', $method, 'The unified gameplay RPC should be invoked.');
        $assertSame('operator-1', $operatorId, 'The operator ID should be forwarded.');
        $assertSame(
            GameType::GAME_TYPE_UNSPECIFIED,
            $request->getGameType(),
            'Unfiltered gameplay queries should not set a game type.'
        );
        $assertSame('', $request->getSymbol(), 'Unfiltered gameplay queries should not set a symbol.');
        $assertSame(
            PlatformTemplateClient::CAPABILITY_READ,
            $capability,
            'The read capability should be forwarded.'
        );

        return [
            (new GetGameplayConfigResponse())->setConfiguration($configurationView),
            (object) ['code' => 0, 'details' => ''],
        ];
    }
);
$template = $getClient->getTemplate('operator-1');
$assertSame(null, $template['draft'], 'Unified gameplay queries should not expose legacy drafts.');
$assertSame(8, $template['current']['version'], 'Template versions should be mapped from gameplay configuration.');
$assertSame('8:0', $template['current']['version_id'], 'Effective versions should become compatibility IDs.');
$assertSame('HIGH_LOW', $template['current']['rules'][0]['game_type'], 'Gameplay rules should be mapped.');
$assertSame('1.80', $template['current']['rules'][0]['fixed_odds']['default_value'], 'Config fields should map to template constraints.');
$orderPageRequest = (new ListAdminOrdersRequest())
    ->setPageNo(2)
    ->setPageSize(20);
$assertSame(2, $orderPageRequest->getPageNo(), 'Admin order queries should support page_no.');
$orderPage = (new AdminOrderPage())
    ->setPageNo(2)
    ->setPageSize(20)
    ->setCount(123)
    ->setExtend([(new PageExtend())->setKey('total_stake')->setValue('0')]);
$assertSame(123, $orderPage->getCount(), 'Admin order pages should expose count.');
$assertSame('total_stake', $orderPage->getExtend()[0]->getKey(), 'Admin order pages should expose extend entries.');
$assertTrue(
    method_exists(PredictionAdminClient::class, 'ListOrders')
        && method_exists(PredictionAdminClient::class, 'ListRounds')
        && method_exists(PredictionAdminClient::class, 'GetOrder'),
    'The generated management client should expose the documented query RPCs.'
);
$assertTrue(
    method_exists(PredictionConfigAdminClient::class, 'GetGameplayConfig')
        && method_exists(PredictionConfigAdminClient::class, 'SaveSymbolConfig'),
    'The generated unified config client should expose the documented config RPCs.'
);

$capturedDraftRequest = null;
$saveClient = new PlatformTemplateClient(
    $configuration,
    static function (string $method, object $request) use (&$capturedDraftRequest): array {
        $capturedDraftRequest = $request;

        return [
            (new SavePlatformTemplateDraftResponse())->setDraft(
                (new PlatformTemplateDraft())
                    ->setDraftId('draft-2')
                    ->setBasedOnVersion(7)
                    ->setRules($request->getRules())
            ),
            (object) ['code' => 0, 'details' => ''],
        ];
    }
);
$saved = $saveClient->saveDraft('operator-1', [
    'client_request_id' => 'request-1',
    'reason' => 'package test',
    'draft_id' => 'draft-1',
    'based_on_version' => 7,
    'rules' => [$upDownData(), $highLowData()],
]);
$savedRules = iterator_to_array($capturedDraftRequest->getRules());
$assertSame('draft-2', $saved['draft']['draft_id'], 'The saved draft should be mapped.');
$assertTrue($savedRules[0]->hasBetOpenSeconds(), 'UP_DOWN requests should include bet-open constraints.');
$assertTrue(!$savedRules[0]->hasFixedOdds(), 'UP_DOWN requests should omit fixed odds.');
$assertTrue($savedRules[1]->hasFixedOdds(), 'HIGH_LOW requests should include fixed odds.');
$assertTrue(!$savedRules[1]->hasBetOpenSeconds(), 'HIGH_LOW requests should omit bet-open constraints.');

$capturedPublishRequest = null;
$publishClient = new PlatformTemplateClient(
    $configuration,
    static function (string $method, object $request) use (&$capturedPublishRequest, $upDownRule): array {
        $capturedPublishRequest = $request;

        return [
            (new PublishPlatformTemplateResponse())
                ->setVersion((new PlatformTemplateVersion())
                    ->setVersionId('version-8')
                    ->setVersion(8)
                    ->setRules([$upDownRule()]))
                ->setReconciliationJob((new TemplateReconciliationJob())
                    ->setJobId('job-8')
                    ->setTemplateVersion(8)
                    ->setAffectedCount(12)),
            (object) ['code' => 0, 'details' => ''],
        ];
    }
);
$published = $publishClient->publish('operator-1', [
    'client_request_id' => 'publish-request-1',
    'reason' => 'package test publish',
    'draft_id' => 'draft-2',
]);
$assertSame(1, $capturedPublishRequest->getSchemaVersion(), 'The schema version should be set.');
$assertSame(8, $published['version']['version'], 'The published version should be mapped.');
$assertSame('job-8', $published['reconciliation_job']['job_id'], 'The reconciliation job should be mapped.');
$assertSame(12, $published['reconciliation_job']['affected_count'], 'Job counters should be mapped.');

$failedClient = new PlatformTemplateClient(
    $configuration,
    static fn (): array => [null, (object) ['code' => 10, 'details' => 'version conflict']]
);
$failure = null;
try {
    $failedClient->getTemplate('operator-1');
} catch (PredictionRpcException $exception) {
    $failure = $exception;
}
$assertTrue($failure instanceof PredictionRpcException, 'Non-zero gRPC statuses should throw a package exception.');
$assertSame(10, $failure?->statusCode(), 'The original gRPC status should be retained.');
$assertSame('version conflict', $failure?->statusDetails(), 'The original gRPC details should be retained.');

fwrite(STDOUT, sprintf("6mm-prediction: %d assertions passed.\n", $assertions));
