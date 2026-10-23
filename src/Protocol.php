<?php

declare(strict_types=1);

namespace SixMm\Prediction;

final class Protocol
{
    public static function directory(): string
    {
        return dirname(__DIR__) . '/proto';
    }

    public static function generationScript(): string
    {
        return dirname(__DIR__) . '/scripts/generate-protos.sh';
    }
}
