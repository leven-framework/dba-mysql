<?php

namespace Leven\DBA\Mock\Query\Filter;

use Leven\DBA\Common\Part\OrderTrait;

trait OrderFilterTrait
{

    use OrderTrait;

    protected function filterOrder(): static
    {
        if(empty($this->order)) return $this;

        // get rid of column definitions temporarily
        $columns = $this->workset[0];
        $columnNames = array_keys($columns);
        unset($this->workset[0]);

        uasort($this->workset, function($a, $b) use ($columnNames) {
            $left = []; $right = [];

            foreach($this->order as $column => $direction){
                $index = array_search($column, $columnNames);
                $left[] = $direction === 'ASC' ? $a[$index] : $b[$index];
                $right[] = $direction === 'ASC' ? $b[$index] : $a[$index];
            }

            return $left <=> $right;
        });

        // restore column definitions
        $this->workset[0] = $columns;

        return $this;
    }

}