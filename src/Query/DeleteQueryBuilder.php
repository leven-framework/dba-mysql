<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\DeleteQueryInterface;
use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Generator\{LimitGeneratorTrait, OrderGeneratorTrait, WhereGeneratorTrait};

class DeleteQueryBuilder extends BaseQueryBuilder implements DeleteQueryInterface
{

    use WhereGeneratorTrait;
    use OrderGeneratorTrait;
    use LimitGeneratorTrait;

    public function getQuery(): Query
    {
        return (new Query('DELETE FROM '))
            ->merge(
                $this->genQueryTable(),
                $this->genQueryWhere(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}