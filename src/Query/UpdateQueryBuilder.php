<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Part\{LimitTrait, OrderTrait, SetTrait, WhereTrait};

class UpdateQueryBuilder extends BaseQueryBuilder
{

    use SetTrait;
    use WhereTrait;
    use OrderTrait;
    use LimitTrait;

    public function getQuery(): Query
    {
        return (new Query('UPDATE '))
            ->merge(
                $this->genQueryTable(),
                $this->genQuerySet(),
                $this->genQueryConds(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}