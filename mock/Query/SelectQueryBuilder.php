<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Query\Filter\ColumnFilterTrait;
use Leven\DBA\Mock\Query\Filter\LimitFilterTrait;
use Leven\DBA\Mock\Query\Filter\OrderFilterTrait;
use Leven\DBA\Mock\Query\Filter\WhereFilterTrait;

class SelectQueryBuilder extends BaseQueryBuilder
{

    use WhereFilterTrait;
    use LimitFilterTrait;
    use OrderFilterTrait;
    use ColumnFilterTrait;

    final protected function formatAssocColumns(): static
    {
        $output = [];
        $columns = array_keys($this->workset[0]);
        foreach($this->workset as $index => $row)
            if($index !== 0) $output[] = array_combine($columns, $row);
        $this->workset = $output;
        return $this;
    }

    public function getQuery(): Query
    {
        $this->prepareWorkset()
            ->filterTable()
            ->filterWhere()
            ->filterOrder()
            ->filterLimit()
            ->filterColumn()
            ->formatAssocColumns()
        ;

        return new Query($this->workset, count($this->workset));
    }

}