#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PROTOC_BIN="${PROTOC_BIN:-$(command -v protoc)}"
GRPC_PHP_PLUGIN_BIN="${GRPC_PHP_PLUGIN_BIN:-$(command -v grpc_php_plugin)}"

"${PROTOC_BIN}" \
  --proto_path="${ROOT_DIR}/proto" \
  --php_out="${ROOT_DIR}/generated" \
  --grpc_out="${ROOT_DIR}/generated" \
  --plugin="protoc-gen-grpc=${GRPC_PHP_PLUGIN_BIN}" \
  "${ROOT_DIR}/proto/prediction/v1/common.proto" \
  "${ROOT_DIR}/proto/prediction/v1/config.proto"

printf 'Prediction PHP gRPC classes regenerated.\n'
