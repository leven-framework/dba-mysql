<?php

namespace Leven\DBA\Common\BuilderPart;

trait SetTrait
{

    protected array $data = [];

    public function set(array|string $dataOrColumn, null|string|bool|int|float $value = null): static
    {
        if(is_array($dataOrColumn)) {
            $this->data = [...$this->data, ...$dataOrColumn];
            return $this;
        }

        $this->data[$dataOrColumn] = $value;

        return $this;
    }

}