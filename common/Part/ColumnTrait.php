<?php

namespace Leven\DBA\Common\Part;

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

}