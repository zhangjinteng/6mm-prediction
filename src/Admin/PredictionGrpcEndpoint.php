<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

final class PredictionGrpcEndpoint
{
    public static function isValid($endpoint): bool
    {
        // Only a configured host:port is allowed, never gRPC resolver URLs or user input.
        if (!is_string($endpoint) || !preg_match('/\A(\[[0-9a-fA-F:.]+\]|[a-zA-Z0-9.-]+):([1-9][0-9]{0,4})\z/', $endpoint, $parts) || (int) $parts[2] > 65535) {
            return false;
        }
        $host = $parts[1];
        if ($host[0] === '[') {
            $address = substr($host, 1, -1);
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                return false;
            }
            $binary = inet_pton($address);
            return $binary !== str_repeat("\0", 16)
                && $binary !== str_repeat("\0", 10)."\xff\xff".str_repeat("\0", 4);
        }
        if (preg_match('/\A[0-9.]+\z/', $host)) {
            return $host !== '0.0.0.0' && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
        }
        return strlen(rtrim($host, '.')) <= 253
            && filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
}
