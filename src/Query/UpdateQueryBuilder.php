<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\UpdateQueryInterface;
use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\Generator\{LimitGeneratorTrait, OrderGeneratorTrait, SetGeneratorTrait, WhereGeneratorTrait};

class UpdateQueryBuilder extends BaseQueryBuilder implements UpdateQueryInterface
{

    use SetGeneratorTrait;
    use WhereGeneratorTrait;
    use OrderGeneratorTrait;
    use LimitGeneratorTrait;

    public function getQuery(): Query
    {
        return (new Query('UPDATE '))
            ->merge(
                $this->genQueryTable(),
                $this->genQuerySet(),
                $this->genQueryWhere(),
                $this->genQueryOrder(),
                $this->genQueryLimit(),
            );
    }

}