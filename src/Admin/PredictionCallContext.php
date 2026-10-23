<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

/** Trusted identity supplied by the host, never extracted from request payloads. */
final class PredictionCallContext
{
    public function __construct(public readonly string $operatorId, public readonly string $traceId)
    {
        if (!PredictionValue::unsigned($operatorId, PredictionValue::INT64_MAX, false) ||
            $traceId === '' || trim($traceId) !== $traceId || mb_strlen($traceId) > 128) {
            throw new PredictionConfigRpcException(16, 'ERROR_CODE_UNAUTHENTICATED', '管理员身份无效');
        }
    }
}
