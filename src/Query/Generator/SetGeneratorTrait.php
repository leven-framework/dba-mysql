<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\BuilderPart\SetTrait;
use Leven\DBA\MySQL\Query;

trait SetGeneratorTrait
{

    use SetTrait;

    protected function genQuerySet(): Query
    {
        if(empty($this->data)) return new Query;

        $query = new Query(' SET ');
        $index = 0;
        foreach($this->data as $column => $value) {
            if($index++ > 0) $query->append(', ');
            $query->append(static::escapeName($column) . ' = ?');
            $query->addParams($value);
        }

        return $query;
    }

}