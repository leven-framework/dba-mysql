<?php

namespace Leven\DBA\Mock\Query\Filter;

use Leven\DBA\Common\Part\LimitTrait;

trait LimitFilterTrait
{

    use LimitTrait;

    protected function filterLimit(): static
    {
        if($this->limit === 0) return $this;

        $count = 0;
        foreach($this->workset as $index => $value) {
            if($index === 0) continue; // column names

            if($count < $this->offset || $count >= $this->limit + $this->offset)
                unset($this->workset[$index]);

            $count++;
        }

        return $this;
    }

}