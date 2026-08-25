<?php

declare(strict_types=1);

use Prediction\V1\DecimalConstraint;
use Prediction\V1\GameType;
use Prediction\V1\GetPlatformTemplateResponse;
use Prediction\V1\IntegerConstraint;
use Prediction\V1\OperationReceipt;
use Prediction\V1\PlatformRuleTemplate;
use Prediction\V1\PlatformTemplateDraft;
use Prediction\V1\PlatformTemplateVersion;
use Prediction\V1\PredictionAdminClient;
use Prediction\V1\PriceRule;
use Prediction\V1\PublishPlatformTemplateResponse;
use Prediction\V1\SaveMerchantSymbolConfigRequest;
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

$response = (new GetPlatformTemplateResponse())
    ->setDraft((new PlatformTemplateDraft())
        ->setDraftId('draft-1')
        ->setBasedOnVersion(7)
        ->setRules([$upDownRule(), $highLowRule()]))
    ->setCurrent((new PlatformTemplateVersion())
        ->setVersionId('version-7')
        ->setVersion(7)
        ->setRules([$upDownRule(), $highLowRule()]));
$getClient = new PlatformTemplateClient(
    $configuration,
    static function (string $method, object $request, string $operatorId, string $capability) use (
        $response,
        $assertSame
    ): array {
        $assertSame('GetPlatformTemplate', $method, 'The expected RPC method should be invoked.');
        $assertSame('operator-1', $operatorId, 'The operator ID should be forwarded.');
        $assertSame(false, $request->getIncludeDraft(), 'Current template queries should not request legacy drafts.');
        $assertSame(
            PlatformTemplateClient::CAPABILITY_READ,
            $capability,
            'The read capability should be forwarded.'
        );

        return [$response, (object) ['code' => 0, 'details' => '']];
    }
);
$template = $getClient->getTemplate('operator-1', 0, false);
$assertSame('UP_DOWN', $template['draft']['rules'][0]['game_type'], 'UP_DOWN rules should be mapped.');
$assertSame(null, $template['draft']['rules'][0]['fixed_odds'], 'Unused UP_DOWN constraints must stay sparse.');
$assertSame('HIGH_LOW', $template['draft']['rules'][1]['game_type'], 'HIGH_LOW rules should be mapped.');
$assertSame(null, $template['draft']['rules'][1]['bet_open_seconds'], 'Unused HIGH_LOW constraints must stay sparse.');

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
$assertTrue(
    method_exists(PredictionAdminClient::class, 'ListOrders')
        && method_exists(PredictionAdminClient::class, 'ListRounds')
        && method_exists(PredictionAdminClient::class, 'GetOrder'),
    'The generated management client should expose the documented query RPCs.'
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
