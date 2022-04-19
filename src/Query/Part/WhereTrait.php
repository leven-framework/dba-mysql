<?php

namespace Leven\DBA\MySQL\Query\Part;

use Leven\DBA\MySQL\Query;
use Leven\DBA\MySQL\Query\WhereBuilder;

trait WhereTrait
{

    /* @var WhereCondition[] $conditions */
    protected array $conditions = [];


    public function andWhere(
        string|callable $columnOrGroup,
        null|string|bool|int|float|array $valueOrOperand = [],
        null|string|bool|int|float|array $value = [],
    ): static
    {
        return $this->whereGeneric(false, $columnOrGroup, $valueOrOperand, $value);
    }

    public function orWhere(
        string|callable $columnOrGroup,
        null|string|bool|int|float|array $valueOrOperand = [],
        null|string|bool|int|float|array $value = [],
    ): static
    {
        return $this->whereGeneric(true, $columnOrGroup, $valueOrOperand, $value);
    }

    public function where(
        string|callable $columnOrGroup,
        null|string|bool|int|float|array $valueOrOperand = [],
        null|string|bool|int|float|array $value = [],
    ): static
    {
        return $this->andWhere($columnOrGroup, $valueOrOperand, $value);
    }


    // INTERNAL

    protected function whereGeneric(
        bool $isOr,
        string|callable $columnOrGroup,
        null|string|bool|int|float|array $valueOrOperand = [],
        null|string|bool|int|float|array $value = [],
        // array types because we need to allow use of null values
    ): static
    {
        if(is_callable($columnOrGroup)){
            $conditionBuilder = new WhereBuilder($isOr);
            $columnOrGroup($conditionBuilder);
            $this->conditions[] = $conditionBuilder;
        }else{
            $this->conditions[] = ($value === []) ?
                new WhereCondition($isOr, $columnOrGroup, $valueOrOperand) :
                new WhereCondition($isOr, $columnOrGroup, $value, $valueOrOperand);
        }
        return $this;
    }


    protected static function genQueryCondsRecursive(array $conditions): Query
    {
        $query = new Query;
        if(empty($conditions)) return $query;

        foreach ($conditions as $index => $condition) {
            if($index !== 0) $query->append($condition->isOr ? ' OR ' : ' AND ');

            if($condition instanceof WhereBuilder) {
                $query->merge(static::genQueryCondsRecursive($condition->getConditions()));
            } else
            if($condition instanceof WhereCondition) {
                $query->append(static::escapeName($condition->column) . ' ' . $condition->operand . ' ?');
                $query->addParams($condition->value);
            }
        }

        return $query->wrap('(', ')');
    }

    protected function genQueryConds(): Query
    {
        $conds = static::genQueryCondsRecursive($this->conditions);
        if($conds->empty()) return new Query;
        $conds->prepend(' WHERE ');
        return $conds;
    }

}