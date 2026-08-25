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

The platform-template client exposes both the current direct-save workflow and
the retained legacy draft workflow:

- `getTemplate()`
- `saveSymbolConfig()`
- `saveDraft()`
- `publish()`

For the direct-save management page, call `getTemplate($operatorId, 0, false)`
and submit the selected gameplay and symbol through `saveSymbolConfig()`.
`includeDraft` defaults to `true` only to keep existing integrations compatible.

It sends the bearer token and trusted operator subject as gRPC metadata. Trace
IDs and RPC permissions are resolved by the prediction service. Failures are reported as
`SixMm\Prediction\Exceptions\PredictionRpcException`, including the original
gRPC status code and details.

## Protocol source

Canonical `common.proto`, `config.proto`, and `admin.proto` files are under
`proto/prediction/v1`. Generated PHP messages and clients for
`PredictionPlatformAdmin`, `PredictionMerchantAdmin`, and `PredictionAdmin`
are committed under `generated`, so production deployments do not need
`protoc` or `grpc_php_plugin`.

Regenerate after updating the protocol files:

```bash
composer generate
composer test
```
