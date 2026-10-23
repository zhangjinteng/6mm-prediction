#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PROTO_ROOT="${PROJECT_ROOT}/proto"
OUTPUT_ROOT="${PREDICTION_PROTO_OUTPUT_DIR:-${PROJECT_ROOT}/generated}"
mkdir -p "${OUTPUT_ROOT}"
PROTOBUF_INCLUDE_DIR="${PROTOBUF_INCLUDE_DIR:-/usr/include}"
PROTOC_BIN="${PROTOC_BIN:-$(command -v protoc || true)}"

if [[ -z "${PROTOC_BIN}" || ! -x "${PROTOC_BIN}" ]]; then
    echo "未找到 protoc，请先安装 Protocol Buffers 编译器。" >&2
    exit 1
fi
if [[ "$("${PROTOC_BIN}" --version)" != "libprotoc 33.6" ]]; then
    echo "Use protoc 33.6 for the supported PHP protobuf runtime; set PROTOC_BIN explicitly." >&2
    exit 1
fi

GRPC_PHP_PLUGIN="${GRPC_PHP_PLUGIN:-$(command -v grpc_php_plugin || true)}"
if [[ -z "${GRPC_PHP_PLUGIN}" || ! -x "${GRPC_PHP_PLUGIN}" ]]; then
    echo "未找到 grpc_php_plugin，请安装 gRPC PHP 代码生成插件或设置 GRPC_PHP_PLUGIN。" >&2
    exit 1
fi

for proto_file in \
    "${PROTO_ROOT}/prediction/v1/common.proto" \
    "${PROTO_ROOT}/prediction/v1/config.proto" \
    "${PROTO_ROOT}/prediction/v1/admin_query.proto" \
    "${PROTO_ROOT}/prediction/v1/market_maker.proto" \
    "${PROTO_ROOT}/prediction/v1/platform_robot.proto" \
    "${PROTO_ROOT}/prediction/v1/routing.proto" \
    "${PROTO_ROOT}/google/rpc/status.proto"; do
    if [[ ! -f "${proto_file}" ]]; then
        echo "未找到 ${proto_file}。" >&2
        exit 1
    fi
done

include_args=("--proto_path=${PROTO_ROOT}")
if [[ -n "${PROTOBUF_DESCRIPTOR_SET:-}" ]]; then
    include_args+=("--descriptor_set_in=${PROTOBUF_DESCRIPTOR_SET}")
fi
if [[ -d "${PROTOBUF_INCLUDE_DIR}" ]]; then
    include_args+=("--proto_path=${PROTOBUF_INCLUDE_DIR}")
fi

# A fresh owned staging directory defines the exact output set of these six inputs.
# Unrelated files already present in OUTPUT_ROOT must never be normalized or copied.
GENERATION_ROOT="$(mktemp -d "${OUTPUT_ROOT}/.prediction-proto.XXXXXX")"
trap 'rm -rf -- "${GENERATION_ROOT}"' EXIT

"${PROTOC_BIN}" \
    "${include_args[@]}" \
    --php_out="${GENERATION_ROOT}" \
    --grpc_out="${GENERATION_ROOT}" \
    --plugin=protoc-gen-grpc="${GRPC_PHP_PLUGIN}" \
    "${PROTO_ROOT}/prediction/v1/common.proto" \
    "${PROTO_ROOT}/prediction/v1/config.proto" \
    "${PROTO_ROOT}/prediction/v1/admin_query.proto" \
    "${PROTO_ROOT}/prediction/v1/market_maker.proto" \
    "${PROTO_ROOT}/prediction/v1/platform_robot.proto" \
    "${PROTO_ROOT}/prediction/v1/routing.proto" \
    "${PROTO_ROOT}/google/rpc/status.proto"

# Normalize only files emitted into this invocation's empty staging directory.
while IFS= read -r -d '' generated_file; do
    "${PHP_BINARY:-php}" -r \
        'file_put_contents($argv[1], rtrim(file_get_contents($argv[1]), "\r\n") . "\n");' \
        "${generated_file}"
    relative_path="${generated_file#"${GENERATION_ROOT}/"}"
    mkdir -p "${OUTPUT_ROOT}/$(dirname "${relative_path}")"
    cp -- "${generated_file}" "${OUTPUT_ROOT}/${relative_path}"
done < <(find "${GENERATION_ROOT}" -type f -name '*.php' -print0)

echo "Prediction Admin protobuf PHP 消息类和 gRPC 客户端已生成。"
