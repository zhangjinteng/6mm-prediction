# Migrating from 1.x to 2.x

Version 2.0.0 adopts the current platform/server prediction protocol. All existing
1.x Git tags are retained without modification. Dependencies constrained to `^1.0`
will not automatically upgrade to this major release.

The earlier `PlatformTemplates\PlatformTemplateClient`, `ClientConfiguration` and
legacy generated platform/merchant-template RPCs are not the 2.x API. Their protocol
has colliding `Prediction\V1` message names and different field meanings. Do not alias
or co-load the generated definitions. Evaluate the server contract before upgrading.

For the current platform application:

1. Require `zhangjinteng/6mm-prediction:2.0.0`.
2. Replace local RPC gateway/transport/mapper/value/exception imports with
   `SixMm\Prediction\Admin\...`, and gateway interface imports with its `Contracts` namespace.
3. Bind a transport with the host's explicit `prediction` configuration array.
4. Keep HTTP administrator-context extraction, permissions, site mappings, order-link
   resolution, source enrichment, validation and database-backed grid queries in the host.
5. After integration tests pass, remove host-owned `Prediction/`, `Google/Rpc/`,
   `GPBMetadata/Prediction/`, `GPBMetadata/Google/Rpc/` and their protocol/generator/autoload
   ownership. Preserve metadata used by unrelated services.
6. Verify reflected SDK/generated classes load from vendor and run isolated HTTP,
   authorization and contract tests. Live verification must be read-only.

Other applications, credentials, endpoints and permissions are not automatically changed.
