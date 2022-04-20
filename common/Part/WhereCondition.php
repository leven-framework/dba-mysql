<?php

namespace Leven\DBA\Common\Part;

class WhereCondition
{

    public function __construct(
        public readonly bool $isOr,
        public readonly string $column,
        public readonly null|string|bool|int|float $value,
        public readonly string $operand = '<=>',
    )
    {
    }

}