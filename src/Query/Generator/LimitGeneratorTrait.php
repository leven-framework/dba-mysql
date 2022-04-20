<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\Part\LimitTrait;
use Leven\DBA\MySQL\Query;

trait LimitGeneratorTrait
{
    use LimitTrait;

    protected function genQueryLimit(): Query
    {
        if($this->limit <= 0) return new Query;

        $query = new Query(" LIMIT $this->limit");
        if($this->offset > 0) $query->append(" OFFSET $this->offset");
        return $query;
    }
}