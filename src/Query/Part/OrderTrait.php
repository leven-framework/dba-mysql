<?php

namespace Leven\DBA\MySQL\Query\Part;

use Leven\DBA\MySQL\Query;

trait OrderTrait
{

    protected array $order = [];

    public function orderAsc(string $column): static
    {
        $this->order[$column] = 'ASC';
        return $this;
    }

    public function orderDesc(string $column): static
    {
        $this->order[$column] = 'DESC';
        return $this;
    }


    // INTERNAL

    protected function genQueryOrder(): Query
    {
        if(empty($this->order)) return new Query;

        return (new Query(' ORDER BY '))
            ->append(implode(', ', array_map(
                fn($column) => (static::escapeName($column) . ' ' . $this->order[$column]),
                array_keys($this->order)
            )));
    }

}