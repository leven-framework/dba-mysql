<?php

namespace Leven\DBA\Common\BuilderPart;

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

}