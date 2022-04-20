<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\MySQL\Query;

class DeleteQueryBuilder extends BaseQueryBuilder
{

    use Query\Generator\WhereGeneratorTrait;
    use Query\Generator\OrderGeneratorTrait;
    use Query\Generator\LimitGeneratorTrait;

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