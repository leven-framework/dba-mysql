<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Part\{ColumnTrait, LimitTrait, OrderTrait, WhereTrait};

class SelectQueryBuilder extends BaseQueryBuilder
{

    use ColumnTrait;
    use WhereTrait;
    use OrderTrait;
    use LimitTrait;

    public function getQuery(): Query
    {
        return (new Query('SELECT '))
            ->merge($this->genQueryColumns())
            ->append(' FROM ')
            ->merge(
                $this->genQueryTable(),
                $this->genQueryConds(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}