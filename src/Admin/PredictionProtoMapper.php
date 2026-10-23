<?php

declare(strict_types=1);

namespace SixMm\Prediction\Admin;

use Google\Protobuf\DescriptorPool;
use Google\Protobuf\FieldDescriptor;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\Message;
use JsonException;

final class PredictionProtoMapper
{
    public function request(string $class, array $payload): Message
    {
        $message = new $class();
        $message->mergeFromJsonString(json_encode($this->toProtoJson($payload), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), false);
        return $message;
    }

    public function toProtoJson(array $value): array
    {
        return $this->mapKeys($value, fn (string $key): string => $this->snakeToCamel($key));
    }

    public function toHttpArray(array $value): array
    {
        return $this->mapKeys($value, fn (string $key): string => $this->camelToSnake($key));
    }

    /**
     * @throws JsonException
     */
    public function messageToHttpArray(Message $message): array
    {
        $decoded = json_decode($message->serializeToJsonString(), true, 512, JSON_THROW_ON_ERROR);
        $completed = $this->completeProtoJson($message, is_array($decoded) ? $decoded : []);

        return $this->toHttpArray($completed);
    }

    private function completeProtoJson(Message $message, array $decoded): array
    {
        $descriptor = DescriptorPool::getGeneratedPool()->getDescriptorByClassName($message::class);
        if ($descriptor === null) {
            return $decoded;
        }

        for ($index = 0; $index < $descriptor->getFieldCount(); $index++) {
            $field = $descriptor->getField($index);
            $jsonKey = $this->snakeToCamel($field->getName());
            $getter = $this->fieldMethod('get', $field->getName());
            $value = $message->{$getter}();

            if ($field->isRepeated()) {
                $decoded[$jsonKey] ??= [];
                if ($field->getType() === GPBType::MESSAGE && !$field->isMap()) {
                    foreach ($value as $itemIndex => $item) {
                        if ($item instanceof Message) {
                            $decoded[$jsonKey][$itemIndex] = $this->completeProtoJson(
                                $item,
                                $decoded[$jsonKey][$itemIndex] ?? []
                            );
                        }
                    }
                }
                continue;
            }

            if ($field->getType() === GPBType::MESSAGE) {
                if ($value instanceof Message) {
                    $decoded[$jsonKey] = $this->completeProtoJson(
                        $value,
                        $decoded[$jsonKey] ?? []
                    );
                }
                continue;
            }

            if (array_key_exists($jsonKey, $decoded)) {
                continue;
            }

            $hazzer = $this->fieldMethod('has', $field->getName());
            if (method_exists($message, $hazzer) && !$message->{$hazzer}()) {
                continue;
            }

            $decoded[$jsonKey] = $this->defaultJsonScalar($field, $value);
        }

        return $decoded;
    }

    private function defaultJsonScalar(FieldDescriptor $field, mixed $value): mixed
    {
        if (in_array($field->getType(), [
            GPBType::INT64,
            GPBType::UINT64,
            GPBType::FIXED64,
            GPBType::SFIXED64,
            GPBType::SINT64,
        ], true)) {
            return (string) $value;
        }

        if ($field->getType() !== GPBType::ENUM) {
            return $value;
        }

        $enum = $field->getEnumType();
        for ($index = 0; $index < $enum->getValueCount(); $index++) {
            $candidate = $enum->getValue($index);
            if ($candidate->getNumber() === $value) {
                return $candidate->getName();
            }
        }

        return $value;
    }

    private function fieldMethod(string $prefix, string $fieldName): string
    {
        return $prefix . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
    }

    private function mapKeys(array $value, callable $keyMapper): array
    {
        $mapped = [];
        foreach ($value as $key => $item) {
            $mappedKey = is_string($key) ? $keyMapper($key) : $key;
            $mapped[$mappedKey] = is_array($item) ? $this->mapKeys($item, $keyMapper) : $item;
        }

        return $mapped;
    }

    private function snakeToCamel(string $value): string
    {
        return (string) preg_replace_callback(
            '/_([a-z0-9])/',
            static fn (array $matches): string => strtoupper($matches[1]),
            $value
        );
    }

    private function camelToSnake(string $value): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
