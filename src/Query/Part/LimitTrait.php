<?php

namespace Leven\DBA\MySQL\Query\Part;

use Leven\DBA\MySQL\Query;

trait LimitTrait
{

    protected int $limit = 0;
    protected int $offset = 0;

    public function limit(int $limitOrOffset, int $limit = 0): static
    {
        if($limit !== 0) $this->offset = $limitOrOffset;
        $this->limit = $limit === 0 ? $limitOrOffset : $limit;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }


    // INTERNAL

    protected function genQueryLimit(): Query
    {
        if($this->limit <= 0) return new Query;

        $query = new Query(" LIMIT $this->limit");
        if($this->offset > 0) $query->append(" OFFSET $this->offset");
        return $query;
    }

}