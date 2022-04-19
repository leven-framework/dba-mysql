<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query\Part\WhereTrait;

class WhereBuilder
{
    use WhereTrait;

    public function __construct(
        public readonly bool $isOr,
    )
    {
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }


}