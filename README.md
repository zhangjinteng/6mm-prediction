# 6mm Prediction

`zhangjinteng/6mm-prediction` provides the versioned prediction protobuf
contracts, generated PHP gRPC classes, and framework-independent clients used
by 6MM administration applications.

Applications should normally consume this package through
`zhangjinteng/6mm-php`. Direct use is intended for shared integration code and
non-Laravel services.

## Install

```bash
composer require zhangjinteng/6mm-prediction
```

Requirements:

- PHP 8.2+
- the PHP `grpc` extension
- `grpc/grpc`
- `google/protobuf`

## Platform template client

```php
use SixMm\Prediction\ClientConfiguration;
use SixMm\Prediction\PlatformTemplates\PlatformTemplateClient;

$client = new PlatformTemplateClient(new ClientConfiguration(
    target: 'prediction.internal:18081',
    token: getenv('PREDICTION_GRPC_TOKEN') ?: '',
    timeoutMicroseconds: 5_000_000,
    tls: false,
));

$template = $client->getTemplate('operator-id');
```

The client currently exposes the platform-template workflow:

- `getTemplate()`
- `saveDraft()`
- `publish()`

It sends the bearer token, operator subject, required capability, and a unique
trace ID as gRPC metadata. Failures are reported as
`SixMm\Prediction\Exceptions\PredictionRpcException`, including the original
gRPC status code and details.

## Protocol source

Canonical protocol files are under `proto/prediction/v1`. Generated PHP files
are committed under `generated`, so production deployments do not need
`protoc` or `grpc_php_plugin`.

Regenerate after updating the protocol files:

```bash
composer generate
composer test
```
