<?php

namespace Leven\DBA\Mock\Structure;

class Column
{

    public function __construct(
        public readonly string $name,
        public readonly ColumnType $type,
        public readonly int $maxLength,
    )
    {
    }

    public static function fromArray(string $key, array|string $value): self
    {
        return new self(
            name: $key,
            type:  ColumnType::fromName(is_array($value) ? ($value[0] ?? '') : $value),
            maxLength: is_array($value) ? ($value[1] ?? 0) : 0
        );
    }

    public function toArray(): array
    {
        return [$this->type->name, $this->maxLength];
    }

    public function validateValue(mixed $value): bool
    {
        return match($this->type){
            ColumnType::MOCK => is_null($value) || is_string($value) || is_bool($value) || is_int($value) || is_float($value),
            ColumnType::INT => is_int($value),
            ColumnType::FLOAT => is_float($value) || is_int($value),
            ColumnType::TEXT => is_string($value) && ($this->maxLength === 0 || strlen($value) <= $this->maxLength),
            ColumnType::JSON => true, // TODO validate
            default => false,
        };
    }

}