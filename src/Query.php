<?php

namespace Leven\DBA\MySQL;

class Query
{

    public function __construct(
        protected string $query = '',
        protected array $params = [],
    )
    {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getParams(): array
    {
        return $this->params;
    }


    // STRING METHODS

    public function empty(): bool
    {
        return empty($this->query);
    }

    public function append(string $string): static
    {
        $this->query .= $string;
        return $this;
    }

    public function prepend(string $string): static
    {
        $this->query = $string . $this->query;
        return $this;
    }

    public function wrap(string $begin, string $end): static
    {
        $this->query = $begin . $this->query . $end;
        return $this;
    }


    // PARAMS METHOD

    public function addParams(null|string|bool|int|float ...$params): static
    {
        $this->params = [
            ...$this->params,
            ...array_map(static::makeBoolInt(...), $params),
        ];
        return $this;
    }


    // WHOLE OBJECT METHOD

    public function merge(Query ...$parts): static
    {
        foreach($parts as $part) {
            $this->append($part->query);
            $this->addParams(...$part->params);
        }
        return $this;
    }


    // HELPER

    // used because of a bug in PDO where false is not casted to int
    protected static function makeBoolInt($value)
    {
        return (is_bool($value) ? (int) $value : $value);
    }

}