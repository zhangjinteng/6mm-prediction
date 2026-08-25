<?php

declare(strict_types=1);

namespace SixMm\Prediction\PlatformTemplates;

use Grpc\ChannelCredentials;
use Prediction\V1\ConfigVersionState;
use Prediction\V1\DecimalConstraint;
use Prediction\V1\GameType;
use Prediction\V1\GetPlatformTemplateRequest;
use Prediction\V1\GetPlatformTemplateResponse;
use Prediction\V1\IntegerConstraint;
use Prediction\V1\OperationReceipt;
use Prediction\V1\PlatformRuleTemplate;
use Prediction\V1\PlatformTemplateDraft;
use Prediction\V1\PlatformTemplateVersion;
use Prediction\V1\PredictionPlatformAdminClient;
use Prediction\V1\PriceRule;
use Prediction\V1\PublishPlatformTemplateRequest;
use Prediction\V1\PublishPlatformTemplateResponse;
use Prediction\V1\ReconciliationJobState;
use Prediction\V1\SavePlatformTemplateDraftRequest;
use Prediction\V1\SavePlatformTemplateDraftResponse;
use Prediction\V1\SavePlatformSymbolConfigRequest;
use Prediction\V1\SavePlatformSymbolConfigResponse;
use Prediction\V1\TemplateReconciliationJob;
use SixMm\Prediction\ClientConfiguration;
use SixMm\Prediction\Exceptions\PredictionRpcException;
use Throwable;

final class PlatformTemplateClient
{
    public const CAPABILITY_READ = 'prediction.platform.template.read';
    public const CAPABILITY_EDIT = 'prediction.platform.template.edit';
    public const CAPABILITY_PUBLISH = 'prediction.platform.template.publish';

    private const SCHEMA_VERSION = 1;

    /** @var \Closure(string, object, string, string): array{0: mixed, 1: mixed} */
    private readonly \Closure $rpcInvoker;

    /** @var \Closure(): string */
    private readonly \Closure $traceIdGenerator;

    /**
     * @param callable(string, object, string, string): array{0: mixed, 1: mixed}|null $rpcInvoker
     * @param callable(): string|null $traceIdGenerator
     */
    public function __construct(
        private readonly ClientConfiguration $configuration,
        ?callable $rpcInvoker = null,
        ?callable $traceIdGenerator = null
    ) {
        $this->rpcInvoker = $rpcInvoker === null
            ? $this->defaultRpcInvoker()
            : \Closure::fromCallable($rpcInvoker);
        $this->traceIdGenerator = $traceIdGenerator === null
            ? static fn (): string => self::uuidV4()
            : \Closure::fromCallable($traceIdGenerator);
    }

    /** @return array{draft: array<string, mixed>|null, current: array<string, mixed>} */
    public function getTemplate(string $operatorId, int $version = 0, bool $includeDraft = true): array
    {
        $request = (new GetPlatformTemplateRequest())
            ->setVersion($version)
            ->setIncludeDraft($includeDraft);
        $response = $this->invoke('GetPlatformTemplate', $request, $operatorId, self::CAPABILITY_READ);

        if (!$response instanceof GetPlatformTemplateResponse || !$response->hasCurrent()) {
            throw new PredictionRpcException('GetPlatformTemplate', 13, 'missing current template');
        }

        return [
            'draft' => $response->hasDraft() ? $this->mapDraft($response->getDraft()) : null,
            'current' => $this->mapVersion($response->getCurrent()),
        ];
    }

    /** @return array{version: array<string, mixed>, receipt: array<string, mixed>|null} */
    public function saveSymbolConfig(string $operatorId, array $data): array
    {
        $request = (new SavePlatformSymbolConfigRequest())
            ->setSchemaVersion(self::SCHEMA_VERSION)
            ->setClientRequestId((string) $data['client_request_id'])
            ->setReason(trim((string) $data['reason']))
            ->setBasedOnVersion((int) $data['based_on_version'])
            ->setGameType($this->gameTypeValue((string) $data['game_type']))
            ->setSymbol((string) $data['symbol'])
            ->setPlatformEnabled((bool) $data['platform_enabled'])
            ->setRules(array_map(fn (array $rule): PlatformRuleTemplate => $this->makeRule($rule), $data['rules']));
        $response = $this->invoke(
            'SavePlatformSymbolConfig',
            $request,
            $operatorId,
            self::CAPABILITY_PUBLISH
        );

        if (!$response instanceof SavePlatformSymbolConfigResponse || !$response->hasVersion()) {
            throw new PredictionRpcException('SavePlatformSymbolConfig', 13, 'missing published version');
        }

        return [
            'version' => $this->mapVersion($response->getVersion()),
            'receipt' => $response->hasReceipt() ? $this->mapReceipt($response->getReceipt()) : null,
        ];
    }

    /** @return array{draft: array<string, mixed>, receipt: array<string, mixed>|null} */
    public function saveDraft(string $operatorId, array $data): array
    {
        $request = (new SavePlatformTemplateDraftRequest())
            ->setSchemaVersion(self::SCHEMA_VERSION)
            ->setClientRequestId((string) $data['client_request_id'])
            ->setReason(trim((string) $data['reason']))
            ->setDraftId((string) ($data['draft_id'] ?? ''))
            ->setBasedOnVersion((int) $data['based_on_version'])
            ->setRules(array_map(fn (array $rule): PlatformRuleTemplate => $this->makeRule($rule), $data['rules']));
        $response = $this->invoke('SavePlatformTemplateDraft', $request, $operatorId, self::CAPABILITY_EDIT);

        if (!$response instanceof SavePlatformTemplateDraftResponse || !$response->hasDraft()) {
            throw new PredictionRpcException('SavePlatformTemplateDraft', 13, 'missing saved draft');
        }

        return [
            'draft' => $this->mapDraft($response->getDraft()),
            'receipt' => $response->hasReceipt() ? $this->mapReceipt($response->getReceipt()) : null,
        ];
    }

    /**
     * @return array{
     *     version: array<string, mixed>,
     *     reconciliation_job: array<string, mixed>|null,
     *     receipt: array<string, mixed>|null
     * }
     */
    public function publish(string $operatorId, array $data): array
    {
        $request = (new PublishPlatformTemplateRequest())
            ->setSchemaVersion(self::SCHEMA_VERSION)
            ->setClientRequestId((string) $data['client_request_id'])
            ->setReason(trim((string) $data['reason']))
            ->setDraftId((string) $data['draft_id']);
        $response = $this->invoke('PublishPlatformTemplate', $request, $operatorId, self::CAPABILITY_PUBLISH);

        if (!$response instanceof PublishPlatformTemplateResponse || !$response->hasVersion()) {
            throw new PredictionRpcException('PublishPlatformTemplate', 13, 'missing published version');
        }

        return [
            'version' => $this->mapVersion($response->getVersion()),
            'reconciliation_job' => $response->hasReconciliationJob()
                ? $this->mapReconciliationJob($response->getReconciliationJob())
                : null,
            'receipt' => $response->hasReceipt() ? $this->mapReceipt($response->getReceipt()) : null,
        ];
    }

    private function invoke(string $method, object $request, string $operatorId, string $capability): object
    {
        try {
            [$response, $status] = ($this->rpcInvoker)($method, $request, $operatorId, $capability);
        } catch (PredictionRpcException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new PredictionRpcException($method, 14, $exception->getMessage(), $exception);
        }

        $statusCode = (int) ($status->code ?? 2);
        if ($statusCode !== 0) {
            throw new PredictionRpcException($method, $statusCode, (string) ($status->details ?? 'unknown'));
        }

        if (!is_object($response)) {
            throw new PredictionRpcException($method, 13, 'empty response');
        }

        return $response;
    }

    /** @return \Closure(string, object, string, string): array{0: mixed, 1: mixed} */
    private function defaultRpcInvoker(): \Closure
    {
        $credentials = $this->configuration->tls
            ? ChannelCredentials::createSsl()
            : ChannelCredentials::createInsecure();
        $client = new PredictionPlatformAdminClient(
            $this->configuration->target,
            ['credentials' => $credentials]
        );

        return function (string $method, object $request, string $operatorId, string $_capability) use ($client): array {
            if ($this->configuration->token === '') {
                throw new PredictionRpcException($method, 16, 'authentication token is not configured');
            }

            return $client->{$method}(
                $request,
                [
                    'authorization' => ["Bearer {$this->configuration->token}"],
                    'x-subject-id' => [$operatorId],
                ],
                ['timeout' => $this->configuration->timeoutMicroseconds]
            )->wait();
        };
    }

    private function makeRule(array $rule): PlatformRuleTemplate
    {
        $message = (new PlatformRuleTemplate())
            ->setGameType($this->gameTypeValue((string) $rule['game_type']))
            ->setSymbol((string) $rule['symbol'])
            ->setDurationSeconds((int) $rule['duration_seconds'])
            ->setEnabledByDefault((bool) $rule['enabled_by_default'])
            ->setMinimumStake($this->makeDecimalConstraint($rule['minimum_stake']))
            ->setMaximumStake($this->makeDecimalConstraint($rule['maximum_stake']))
            ->setSettlementDisplaySeconds((int) $rule['settlement_display_seconds'])
            ->setPriceRule((new PriceRule())
                ->setMaxAgeMilliseconds((int) $rule['price_rule']['max_age_milliseconds'])
                ->setLateArrivalGraceMilliseconds((int) $rule['price_rule']['late_arrival_grace_milliseconds']));

        if (($rule['game_type'] ?? '') === 'UP_DOWN') {
            $message
                ->setBetOpenSeconds($this->makeIntegerConstraint($rule['bet_open_seconds']))
                ->setTargetPayoutRate($this->makeDecimalConstraint($rule['target_payout_rate']))
                ->setMinimumOdds($this->makeDecimalConstraint($rule['minimum_odds']))
                ->setMaximumOdds($this->makeDecimalConstraint($rule['maximum_odds']));
        } else {
            $message->setFixedOdds($this->makeDecimalConstraint($rule['fixed_odds']));
        }

        return $message;
    }

    private function makeDecimalConstraint(array $constraint): DecimalConstraint
    {
        return (new DecimalConstraint())
            ->setDefaultValue((string) $constraint['default_value'])
            ->setMinimumValue((string) $constraint['minimum_value'])
            ->setMaximumValue((string) $constraint['maximum_value'])
            ->setMerchantEditable((bool) $constraint['merchant_editable']);
    }

    private function makeIntegerConstraint(array $constraint): IntegerConstraint
    {
        return (new IntegerConstraint())
            ->setDefaultValue((int) $constraint['default_value'])
            ->setMinimumValue((int) $constraint['minimum_value'])
            ->setMaximumValue((int) $constraint['maximum_value'])
            ->setMerchantEditable((bool) $constraint['merchant_editable']);
    }

    /** @return array<string, mixed> */
    private function mapDraft(PlatformTemplateDraft $draft): array
    {
        return [
            'draft_id' => $draft->getDraftId(),
            'based_on_version' => (int) $draft->getBasedOnVersion(),
            'rules' => $this->mapRules($draft->getRules()),
            'updated_at_ms' => (int) $draft->getUpdatedAtMs(),
        ];
    }

    /** @return array<string, mixed> */
    private function mapVersion(PlatformTemplateVersion $version): array
    {
        return [
            'version_id' => $version->getVersionId(),
            'version' => (int) $version->getVersion(),
            'state' => $this->shortEnumName(ConfigVersionState::name($version->getState()), 'CONFIG_VERSION_STATE_'),
            'rules' => $this->mapRules($version->getRules()),
            'created_at_ms' => (int) $version->getCreatedAtMs(),
            'published_at_ms' => (int) $version->getPublishedAtMs(),
            'activated_at_ms' => $version->hasActivatedAtMs() ? (int) $version->getActivatedAtMs() : null,
        ];
    }

    /** @return list<array<string, mixed>> */
    private function mapRules(iterable $rules): array
    {
        $result = [];
        foreach ($rules as $rule) {
            $result[] = $this->mapRule($rule);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function mapRule(PlatformRuleTemplate $rule): array
    {
        return [
            'game_type' => $this->gameTypeName($rule->getGameType()),
            'symbol' => $rule->getSymbol(),
            'duration_seconds' => (int) $rule->getDurationSeconds(),
            'enabled_by_default' => $rule->getEnabledByDefault(),
            'bet_open_seconds' => $rule->hasBetOpenSeconds()
                ? $this->mapIntegerConstraint($rule->getBetOpenSeconds())
                : null,
            'target_payout_rate' => $rule->hasTargetPayoutRate()
                ? $this->mapDecimalConstraint($rule->getTargetPayoutRate())
                : null,
            'minimum_odds' => $rule->hasMinimumOdds()
                ? $this->mapDecimalConstraint($rule->getMinimumOdds())
                : null,
            'maximum_odds' => $rule->hasMaximumOdds()
                ? $this->mapDecimalConstraint($rule->getMaximumOdds())
                : null,
            'fixed_odds' => $rule->hasFixedOdds()
                ? $this->mapDecimalConstraint($rule->getFixedOdds())
                : null,
            'minimum_stake' => $rule->hasMinimumStake()
                ? $this->mapDecimalConstraint($rule->getMinimumStake())
                : null,
            'maximum_stake' => $rule->hasMaximumStake()
                ? $this->mapDecimalConstraint($rule->getMaximumStake())
                : null,
            'settlement_display_seconds' => (int) $rule->getSettlementDisplaySeconds(),
            'price_rule' => $rule->hasPriceRule() ? [
                'max_age_milliseconds' => (int) $rule->getPriceRule()->getMaxAgeMilliseconds(),
                'late_arrival_grace_milliseconds' => (int) $rule->getPriceRule()->getLateArrivalGraceMilliseconds(),
            ] : null,
        ];
    }

    /** @return array<string, mixed> */
    private function mapDecimalConstraint(DecimalConstraint $constraint): array
    {
        return [
            'default_value' => $constraint->getDefaultValue(),
            'minimum_value' => $constraint->getMinimumValue(),
            'maximum_value' => $constraint->getMaximumValue(),
            'merchant_editable' => $constraint->getMerchantEditable(),
        ];
    }

    /** @return array<string, mixed> */
    private function mapIntegerConstraint(IntegerConstraint $constraint): array
    {
        return [
            'default_value' => (int) $constraint->getDefaultValue(),
            'minimum_value' => (int) $constraint->getMinimumValue(),
            'maximum_value' => (int) $constraint->getMaximumValue(),
            'merchant_editable' => $constraint->getMerchantEditable(),
        ];
    }

    /** @return array<string, mixed> */
    private function mapReceipt(OperationReceipt $receipt): array
    {
        return [
            'operation_id' => $receipt->getOperationId(),
            'client_request_id' => $receipt->getClientRequestId(),
            'target_type' => $receipt->getTargetType(),
            'target_id' => $receipt->getTargetId(),
            'result' => $receipt->getResult(),
            'error_code' => $receipt->getErrorCode(),
            'created_at_ms' => (int) $receipt->getCreatedAtMs(),
            'completed_at_ms' => (int) $receipt->getCompletedAtMs(),
        ];
    }

    /** @return array<string, mixed> */
    private function mapReconciliationJob(TemplateReconciliationJob $job): array
    {
        return [
            'job_id' => $job->getJobId(),
            'template_version' => (int) $job->getTemplateVersion(),
            'state' => $this->shortEnumName(
                ReconciliationJobState::name($job->getState()),
                'RECONCILIATION_JOB_STATE_'
            ),
            'scanned_count' => (int) $job->getScannedCount(),
            'affected_count' => (int) $job->getAffectedCount(),
            'updated_count' => (int) $job->getUpdatedCount(),
            'failed_count' => (int) $job->getFailedCount(),
        ];
    }

    private function gameTypeValue(string $name): int
    {
        return match ($name) {
            'UP_DOWN' => GameType::GAME_TYPE_UP_DOWN,
            'HIGH_LOW' => GameType::GAME_TYPE_HIGH_LOW,
            default => GameType::GAME_TYPE_UNSPECIFIED,
        };
    }

    private function gameTypeName(int $value): string
    {
        return match ($value) {
            GameType::GAME_TYPE_UP_DOWN => 'UP_DOWN',
            GameType::GAME_TYPE_HIGH_LOW => 'HIGH_LOW',
            default => 'UNSPECIFIED',
        };
    }

    private function shortEnumName(string $name, string $prefix): string
    {
        return str_starts_with($name, $prefix) ? substr($name, strlen($prefix)) : $name;
    }

    private static function uuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
