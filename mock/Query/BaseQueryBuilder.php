<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Common\AdapterResponse;
use Leven\DBA\Mock\MockAdapter;
use Leven\DBA\Mock\Query;
use Leven\DBA\Mock\Structure\Table;

abstract class BaseQueryBuilder
{

    public function __construct(
        public MockAdapter $database,
        public string      $table,
    )
    {
    }

    abstract public function getQuery(): Query;

    final public function execute(): AdapterResponse
    {
        return $this->database->executeQuery($this->getQuery());
    }


    final protected function getTableCopy(): Table
    {
        return $this->database->getStore()->getTableCopy($this->table);
    }

    final protected function pipe(mixed $initialValue, callable ...$callables): mixed
    {
        return array_reduce($callables, fn($carry, $item) => $item($carry), $initialValue);
    }

    final protected function getRowIndices(Table $table): array
    {
        foreach($table->getRows() as $index => $row)
            $output[] = $index;

        return $output ?? [];
    }

}