<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\InsertQueryInterface;
use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Generator\SetGeneratorTrait;

class InsertQueryBuilder extends BaseQueryBuilder implements InsertQueryInterface
{

    use SetGeneratorTrait;

    public function getQuery(): Query
    {
        return (new Query('INSERT INTO '))
            ->merge(
                $this->genQueryTable(),
                $this->genQuerySet(),
            );
    }

}