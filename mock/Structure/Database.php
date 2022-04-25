<?php

namespace Leven\DBA\Mock\Structure;

use Closure;

class Database
{

    /** @var Table[] $tables */
    protected array $tables = [];

    public function __construct(
        array|Closure      $restore = [],
        protected ?Closure $onUpdate = null,
    )
    {
        if(is_callable($restore)) $restore = $restore();
        $this->fromArray($restore);
    }


    protected function fromArray(array $array): void
    {
        foreach ($array as $tableName => $tableContent)
            $this->tables[$tableName] = Table::fromArray($tableName, $tableContent);
    }

    protected function toArray(): array
    {
        foreach ($this->tables as $table)
            $output[$table->name] = $table->toArray();

        return $output ?? [];
    }


    public function getTableCopy(string $tableName): Table
    {
        return clone $this->tables[$tableName];
    }

    public function replaceTable(Table $table): void
    {
        $this->tables[$table->name] = $table;

        if(is_callable($this->onUpdate))
            ($this->onUpdate)($this->toArray());
    }

}