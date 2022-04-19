<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Part\{LimitTrait, OrderTrait, WhereTrait};

class DeleteQueryBuilder extends BaseQueryBuilder
{

    use WhereTrait;
    use OrderTrait;
    use LimitTrait;

    public function getQuery(): Query
    {
        return (new Query('DELETE FROM '))
            ->merge(
                $this->genQueryTable(),
                $this->genQueryConds(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}