<?php

namespace Leven\DBA\Mock\Query;

use Leven\DBA\Common\DatabaseAdapterResponse;
use Leven\DBA\Mock\MockDatabase;
use Leven\DBA\Mock\Query;

abstract class BaseQueryBuilder
{

    protected array $workset;

    public function __construct(
        public MockDatabase $database,
        public string $table,
    )
    {
    }

    abstract public function getQuery(): Query;

    final public function execute(): DatabaseAdapterResponse
    {
        return $this->database->executeQuery($this->getQuery());
    }


    final protected function prepareWorkset(): static
    {
        $this->workset = $this->database->getStore();
        return $this;
    }

    final protected function clearWorkset(): static
    {
        $this->workset = [];
        return $this;
    }

    final protected function filterTable(): static
    {
        $this->workset = $this->workset[$this->table];
        return $this;
    }

    final protected function worksetGetRowIndices(): array
    {
        foreach($this->workset as $index => $row)
            if($index !== 0) $output[] = $index;
        return $output??[];
    }

    final protected function filterDeleteRowIndicesFromTable(string $table, int ...$indices): static
    {
        foreach($indices as $index)
            unset($this->workset[$table][$index]);

        return $this;
    }

}