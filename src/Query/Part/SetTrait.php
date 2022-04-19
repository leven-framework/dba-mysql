<?php

namespace Leven\DBA\MySQL\Query\Part;

use Leven\DBA\MySQL\Query;

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


    // INTERNAL

    protected function genQuerySet(): Query
    {
        if(empty($this->data)) throw new \Exception('no set data');

        $query = new Query(' SET ');
        $index = 0;
        foreach($this->data as $column => $value) {
            if($index++ > 0) $query->append(', ');
            $query->append(static::escapeName($column) . ' = ?');
            $query->addParams($value);
        }

        return $query;


    }

}