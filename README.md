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

The platform-template client reads the current gameplay configuration through
`PredictionConfigAdmin.GetGameplayConfig`. The request intentionally leaves
`game_type` and `symbol` unset so the service returns every gameplay rule. The
result is mapped to the existing `current.rules` shape for host compatibility.
The client also retains the existing platform mutation workflows:

- `getTemplate()`
- `saveSymbolConfig()`
- `saveDraft()`
- `publish()`

For the direct-save management page, call `getTemplate($operatorId)` and submit
the selected gameplay and symbol through `saveSymbolConfig()`.

It sends the bearer token and trusted operator subject as gRPC metadata. Trace
IDs and RPC permissions are resolved by the prediction service. Failures are reported as
`SixMm\Prediction\Exceptions\PredictionRpcException`, including the original
gRPC status code and details.

## Protocol source

Canonical `common.proto`, `config.proto`, and `admin.proto` files are under
`proto/prediction/v1`. Generated PHP messages and clients for
`PredictionConfigAdmin`, `PredictionPlatformAdmin`, `PredictionMerchantAdmin`,
and `PredictionAdmin` are committed under `generated`, so production
deployments do not need `protoc` or `grpc_php_plugin`.

The unified `PredictionConfigAdmin` service exposes the current gameplay
configuration page RPCs:

- `GetGameplayConfig`
- `SaveSymbolConfig`

`GameplayConfigRule` now also exposes flat page fields such as `game_type`,
`symbol`, `duration_seconds`, and config field objects with `value`,
`template_value`, `merchant_editable`, and `overridden`, so platform and
merchant pages can consume the same response shape.

Regenerate after updating the protocol files:

```bash
composer generate
composer test
```
