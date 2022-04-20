<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\Part\{ColumnTrait};
use Leven\DBA\Common\Part\LimitTrait;
use Leven\DBA\Common\Part\OrderTrait;
use Leven\DBA\Common\Part\WhereTrait;
use Leven\DBA\MySQL\Query;

class SelectQueryBuilder extends BaseQueryBuilder
{

    use Query\Generator\ColumnGeneratorTrait;
    use Query\Generator\WhereGeneratorTrait;
    use Query\Generator\OrderGeneratorTrait;
    use Query\Generator\LimitGeneratorTrait;

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