<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\Part\{SetTrait};
use Leven\DBA\MySQL\Query;

class InsertQueryBuilder extends BaseQueryBuilder
{

    use Query\Generator\SetGeneratorTrait;

    public function getQuery(): Query
    {
        return (new Query('INSERT INTO '))
            ->merge(
                $this->genQueryTable(),
                $this->genQuerySet(),
            );
    }

}