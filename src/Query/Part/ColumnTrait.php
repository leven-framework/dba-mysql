<?php

namespace Leven\DBA\MySQL\Query\Part;

use Leven\DBA\MySQL\Query;

trait ColumnTrait
{

    protected array|string $columns = [];

    public function columns(string ...$columns): static
    {
        $this->columns = [...$this->columns, ...$columns];
        return $this;
    }

    public function rawColumns(string $columns): static
    {
        $this->columns = $columns;
        return $this;
    }


    // INTERNAL

    protected function genQueryColumns(): Query
    {
        if(empty($this->columns)) return new Query('*');

        return new Query(is_string($this->columns)
            ? $this->columns
            : implode( ',', array_map(static::escapeName(...), $this->columns) )
        );
    }

}