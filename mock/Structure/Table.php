<?php

namespace Leven\DBA\Mock\Structure;

use Exception;

class Table
{

    /** @var Column[] $columns */
    protected array $columns;

    protected array $rows;


    public function __construct(
        public readonly string $name,
    )
    {
    }

    public static function fromArray(string $key, array $value): self
    {
        $table = new self($key);

        $table->columnsFromArray($value[0]);

        foreach ($value as $index => $row)
            if ($index !== 0) // skip column definitions
                $table->addRow($row);

        return $table;
    }

    protected function columnsFromArray(array $array): void
    {
        foreach ($array as $columnName => $columnProps)
            $this->columns[$columnName] = Column::fromArray($columnName, $columnProps);
    }


    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumnNames(): array
    {
        return array_keys($this->columns);
    }

    public function getColumnIndex(string $columnName): int
    {
        return array_search($columnName, $this->getColumnNames());
    }

    public function getRows(): array
    {
        return $this->rows;
    }


    public function toArray(): array
    {
        return [ $this->columnsToArray(), ...$this->getRows() ];
    }

    protected function columnsToArray(): array
    {
        foreach ($this->columns as $column)
            $output[$column->name] = $column->toArray();

        return $output ?? [];
    }


    public function addColumn(Column $column): void
    {
        $this->columns[] = $column;
    }

    public function addRow(array $row): void
    {
        foreach($row as $index => $field) {
            $column = $this->getColumnNames()[$index];
            if (!$this->columns[$column]->validateValue($field))
                throw new Exception("value is not valid for column '$column'");
        }

        $this->rows[] = $row;
    }

    public function mergeRow(int $index, array $row): void
    {
        $this->rows[$index] = $row + $this->rows[$index];

        // + messes up the order and the array becomes associative for some reason
        ksort($this->rows[$index]);
    }


    // filter helpers

    public function deleteColumn(string ...$columnNames): void
    {
        foreach($columnNames as $columnName) {
            $columnIndex = $this->getColumnIndex($columnName);

            // delete column
            unset($this->columns[$columnName]);

            // delete each field for this column
            foreach($this->rows as &$row) unset($row[$columnIndex]);
        }
    }

    public function deleteRow(int ...$indexes): void
    {
        foreach($indexes as $index)
            unset($this->rows[$index]);
    }

    public function uasortRows(callable $callback): void
    {
        uasort($this->rows, $callback);
    }

}