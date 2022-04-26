<?php

namespace Leven\DBA\Common\BuilderPart;

class WhereGroup
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