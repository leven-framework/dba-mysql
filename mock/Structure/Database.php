<?php

namespace Leven\DBA\Mock\Structure;

class Database
{

    /** @var Table[] $tables */
    protected array $tables = [];

    public static function fromArray(array $array): static
    {
        $instance = new static;

        foreach ($array as $tableName => $tableContent)
            $instance->tables[$tableName] = Table::fromArray($tableName, $tableContent);

        return $instance;
    }

    public function toArray(): array
    {
        foreach ($this->tables as $table)
            $output[$table->name] = $table->toArray();

        return $output ?? [];
    }


    public function getTable(string $tableName): Table
    {
        return $this->tables[$tableName];
    }

}