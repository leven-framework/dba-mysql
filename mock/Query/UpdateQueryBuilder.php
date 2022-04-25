<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Common\UpdateQueryInterface;
use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Query\Filter\LimitFilterTrait;
use Leven\DBA\Mock\Query\Filter\OrderFilterTrait;
use Leven\DBA\Mock\Query\Filter\SetFilterTrait;
use Leven\DBA\Mock\Query\Filter\WhereFilterTrait;
use Leven\DBA\Mock\Structure\Database;

class UpdateQueryBuilder extends BaseQueryBuilder implements UpdateQueryInterface
{

    use SetFilterTrait;
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

            $row = $this->transformDataToRow($table, true);
            foreach($indices as $index) $table->mergeRow($index, $row);

            $store->replaceTable($table);
        };

        return new Query(count($indices), [], $update);
    }

}