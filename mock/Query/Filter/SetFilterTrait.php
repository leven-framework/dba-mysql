<?php

namespace Leven\DBA\Mock\Query\Filter;

use Leven\DBA\Common\BuilderPart\SetTrait;
use Leven\DBA\Mock\Structure\Table;

trait SetFilterTrait
{

    use SetTrait;

    protected function transformDataToRow(Table $table, bool $allowMissingColumns = false): array
    {
        foreach($this->data as $column => $value)
            $output[$table->getColumnIndex($column)] = $value;

        // TODO: allowMissingColumns (for update query)

        return $output ?? [];
    }

}