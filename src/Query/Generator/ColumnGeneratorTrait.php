<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\BuilderPart\ColumnTrait;
use Leven\DBA\MySQL\Query;

trait ColumnGeneratorTrait
{

    use ColumnTrait;

    protected function genQueryColumns(): Query
    {
        if(empty($this->columns)) return new Query('*');

        return new Query(is_string($this->columns)
            ? $this->columns
            : implode( ',', array_map(static::escapeName(...), $this->columns) )
        );
    }

}