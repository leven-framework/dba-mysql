<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Common;
use Leven\DBA\Common\DeleteQueryInterface;
use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Query\Filter\LimitFilterTrait;
use Leven\DBA\Mock\Query\Filter\OrderFilterTrait;
use Leven\DBA\Mock\Query\Filter\WhereFilterTrait;
use Leven\DBA\Mock\Structure\Database;

class DeleteQueryBuilder extends BaseQueryBuilder implements DeleteQueryInterface
{

    use WhereFilterTrait;
    use LimitFilterTrait;
    use OrderFilterTrait;

    public function getQuery(): Query
    {
        $indices = $this->pipe(
            $this->getTableCopy(),
            $this->filterWhere(...),
            $this->filterOrder(...),
            $this->filterLimit(...),
            $this->getRowIndices(...),
        );

        $update = function(Database $store) use ($indices) {
            $table = $store->getTableCopy($this->table);
            $table->deleteRow(...$indices);
            $store->replaceTable($table);
        };

        return new Query(count($indices), [], $update);
    }

}