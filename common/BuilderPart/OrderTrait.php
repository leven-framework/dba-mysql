<?php

namespace Leven\DBA\Common\BuilderPart;

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

}