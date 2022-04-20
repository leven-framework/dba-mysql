<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\Part\{SetTrait};
use Leven\DBA\Common\Part\LimitTrait;
use Leven\DBA\Common\Part\OrderTrait;
use Leven\DBA\Common\Part\WhereTrait;
use Leven\DBA\MySQL\Query;

class UpdateQueryBuilder extends BaseQueryBuilder
{

    use Query\Generator\SetGeneratorTrait;
    use Query\Generator\WhereGeneratorTrait;
    use Query\Generator\OrderGeneratorTrait;
    use Query\Generator\LimitGeneratorTrait;

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