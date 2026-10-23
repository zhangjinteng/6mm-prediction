<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class PredictionValue
{
    public const INT64_MAX = '9223372036854775807';
    public const UINT64_MAX = '18446744073709551615';
    public static function unsigned($value, string $maximum = self::INT64_MAX, bool $zero = true): bool
    {
        if (!is_string($value) || !preg_match($zero ? '/^(0|[1-9][0-9]*)$/D' : '/^[1-9][0-9]*$/D', $value)) {
            return false;
        }
        return strlen($value) < strlen($maximum) || strlen($value) === strlen($maximum) && strcmp($value, $maximum) <= 0;
    }
    public static function text($value, int $limit): bool
    {
        return is_string($value) && $value !== '' && $value === trim($value) && mb_check_encoding($value, 'UTF-8') && mb_strlen($value) <= $limit;
    }
}
