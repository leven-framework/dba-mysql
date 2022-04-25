<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\SelectQueryInterface;
use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Generator\{
    ColumnGeneratorTrait,
    LimitGeneratorTrait,
    OrderGeneratorTrait,
    WhereGeneratorTrait
};

class SelectQueryBuilder extends BaseQueryBuilder implements SelectQueryInterface
{

    use ColumnGeneratorTrait;
    use WhereGeneratorTrait;
    use OrderGeneratorTrait;
    use LimitGeneratorTrait;

    public function getQuery(): Query
    {
        return (new Query('SELECT '))
            ->merge($this->genQueryColumns())
            ->append(' FROM ')
            ->merge(
                $this->genQueryTable(),
                $this->genQueryWhere(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}