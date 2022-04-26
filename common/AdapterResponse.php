<?php namespace Leven\DBA\Common;

use ArrayIterator, ArrayObject, IteratorAggregate;

class AdapterResponse implements IteratorAggregate {

    public function __construct(
        public readonly int $count,
        public readonly array $rows = [],
        public readonly int|string|null $lastId = null,
    )
    {
    }

    public function getIterator(): ArrayIterator
    {
        $o = new ArrayObject($this->rows);
        return $o->getIterator();
    }

}