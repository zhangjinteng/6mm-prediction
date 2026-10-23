# 6mm Prediction SDK — 2.x

`zhangjinteng/6mm-prediction` is a framework-independent PHP SDK for the current
6MM prediction administration RPCs. It owns protocol sources, generated classes,
request/response mapping, transport and five gateway interfaces. It does not read
Laravel configuration, HTTP requests, environment files or databases.

## Installation

```bash
composer require zhangjinteng/6mm-prediction:2.0.0
```

Requires PHP 8.2+, ext-grpc, ext-json, ext-mbstring, grpc/grpc and google/protobuf.
Add the VCS repository https://github.com/zhangjinteng/6mm-prediction.git to the host
composer.json if your registry has not indexed it.

## Explicit configuration and trusted context

```php
use SixMm\Prediction\Admin\PredictionAdminTransport;
use SixMm\Prediction\Admin\GrpcPredictionConfigGateway;

$transport = new PredictionAdminTransport(configuration: [
    'environment' => 'production',
    'grpc_address' => 'prediction.internal:18081',
    'timeout_ms' => 8000,
    'admin' => ['access' => 'read_only', 'token' => $serviceToken],
]);
$client = new GrpcPredictionConfigGateway($transport);
$result = $client->getConfiguration('UP_DOWN', 'BTCUSDT', $trustedOperatorId, $traceId);
```

The host must supply trusted positive-int64 operator IDs, trace IDs and credentials,
never user-selected identity. Missing/invalid configuration fails closed. The endpoint
must be configured host:port, not a resolver URL. Transport uses the existing private
service's insecure gRPC channel: run only on a trusted protected network. Protecting
the network and authorizing menus/sites/operators remains the host's responsibility.

Access is disabled, read_only or read_write. Writes require read_write; deadlines are
bounded and automatic retries disabled. On uncertain outcomes retain/replay the original
client request ID, payload, version and audit reason. Never create a replacement request.
The SDK itself never automatically replays a write.

## Clients

Classes and interfaces are under `SixMm\Prediction\Admin` and its `Contracts` namespace:

- `GrpcPredictionConfigGateway`: list/get/save symbol configurations.
- `GrpcPredictionOperationGateway`: rounds and current/history order queries.
- `GrpcPredictionMarketMakerGateway`: symbol/room strategies, bindings, readiness/runtime.
- `GrpcPredictionRoutingGateway`: query/prepare/commit/cancel site bindings.
- `GrpcPredictionPlatformRobotGateway`: robots, balances, operations and auto-deposit policies.

Each implements the correspondingly named `Prediction...Gateway` interface with the
existing platform method signatures. Bind those interfaces in the host and inject an
explicitly configured singleton transport. Controllers, HTTP responses, user/source
enrichment and PostgreSQL-backed grid queries stay in the application.

Results are snake_case arrays, preserving optional presence, zero/false/default values
and exact 64-bit/decimal strings. `PredictionConfigRpcException` exposes
`grpcStatusCode()`, `errorCode()` and `detail()`. Structured allowlisted business
diagnostics are surfaced instead of raw gRPC failures.

## Protocol ownership and tests

Canonical files are in `proto/prediction/v1`, rich-status inputs in `proto/google`,
and generated PHP in `generated`. Current `Prediction\V1` wire types and service paths
are unchanged. `SixMm\Prediction\Protocol` locates the installed proto directory and
generator. Do not co-load another incompatible generated `Prediction\V1` tree.

```bash
composer install
composer generate # requires protoc 33.6 and grpc_php_plugin
composer check
```

Set PROTOC_BIN to a protoc 33.6 executable when the system compiler is a different
version. The generator refuses incompatible compilers before modifying generated files.
Production does not need protobuf compilers. Tests inject invokers and fixtures; they
never call a business-write endpoint. See [MIGRATION-2.0.md](MIGRATION-2.0.md) for the
approved major-version boundary. Existing 1.x tags remain unchanged.
