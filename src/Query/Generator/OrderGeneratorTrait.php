<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\BuilderPart\OrderTrait;
use Leven\DBA\MySQL\Query;

trait OrderGeneratorTrait
{

    use OrderTrait;

    protected function genQueryOrder(): Query
    {
        if(empty($this->order)) return new Query;

        return (new Query(' ORDER BY '))
            ->append(implode(', ', array_map(
                fn($column) => (static::escapeName($column) . ' ' . $this->order[$column]),
                array_keys($this->order)
            )));
    }

}