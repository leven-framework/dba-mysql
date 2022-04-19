<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Part\{SetTrait};

class InsertQueryBuilder extends BaseQueryBuilder
{

    use SetTrait;

    public function getQuery(): Query
    {
        return (new Query('INSERT INTO '))
            ->merge(
                $this->genQueryTable(),
                $this->genQuerySet(),
            );
    }

}