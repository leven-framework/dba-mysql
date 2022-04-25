<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Common;
use Leven\DBA\Common\InsertQueryInterface;
use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Query\Filter\SetFilterTrait;
use Leven\DBA\Mock\Structure\Database;

class InsertQueryBuilder extends BaseQueryBuilder implements InsertQueryInterface
{

    use SetFilterTrait;

    public function getQuery(): Query
    {
        $update = function(Database $store) {
            $table = $store->getTableCopy($this->table);
            $table->addRow($this->transformDataToRow($table));
            $store->replaceTable($table);
        };

        return new Query(1, [], $update);
    }

}