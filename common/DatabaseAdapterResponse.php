<?php namespace Leven\DBA\Common;

use ArrayIterator, ArrayObject, IteratorAggregate;
use Leven\DBA\MySQL\Query;

class DatabaseAdapterResponse implements IteratorAggregate {

    public function __construct(
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