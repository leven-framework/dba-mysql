<?php namespace Leven\DBA\MySQL;

use ArrayIterator;
use ArrayObject;
use IteratorAggregate;

class DatabaseAdapterResponse implements IteratorAggregate {

    public function __construct(
        public readonly Query $query,
        public readonly int $count,
        public readonly array $rows = [],
        public readonly int|string|null $lastID = null,
    )
    {
    }

    public function getIterator(): ArrayIterator
    {
        $o = new ArrayObject($this->rows);
        return $o->getIterator();
    }

}