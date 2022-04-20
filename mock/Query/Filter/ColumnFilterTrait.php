<?php

namespace Leven\DBA\Mock\Query\Filter;

use Leven\DBA\Common\Part\ColumnTrait;

trait ColumnFilterTrait
{

    use ColumnTrait;

    protected function filterColumn(): static
    {
        if(is_string($this->columns) || empty($this->columns)) return $this;

        // iterate through all column names
        foreach(array_keys($this->workset[0]) as $index => $column){
            if(in_array($column, $this->columns))
                continue; // column is in keep list

            // get rid of column definition
            unset($this->workset[0][$column]);

            // get rid of this column value in every row
            foreach($this->workset as $rowIndex => $row)
                if($rowIndex !== 0) // column definitions
                    unset($this->workset[$rowIndex][$index]);
        }

        return $this;
    }

}