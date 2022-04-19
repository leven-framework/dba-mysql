<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;

class DescribeQueryBuilder extends BaseQueryBuilder
{

    public function getQuery(): Query
    {
        return (new Query('DESCRIBE '))
            ->merge($this->genQueryTable());
    }

}