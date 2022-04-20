<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Query\Filter\LimitFilterTrait;
use Leven\DBA\Mock\Query\Filter\OrderFilterTrait;
use Leven\DBA\Mock\Query\Filter\WhereFilterTrait;

class DeleteQueryBuilder extends BaseQueryBuilder
{

    use WhereFilterTrait;
    use LimitFilterTrait;
    use OrderFilterTrait;

    public function getQuery(): Query
    {
        $this->prepareWorkset()
            ->filterTable()
            ->filterWhere()
            ->filterOrder()
            ->filterLimit()
        ;

        $indicesToDelete = $this->worksetGetRowIndices();
        $this->prepareWorkset()
            ->filterDeleteRowIndicesFromTable($this->table, ...$indicesToDelete);

        return new Query([], count($indicesToDelete), $this->workset);
    }

}