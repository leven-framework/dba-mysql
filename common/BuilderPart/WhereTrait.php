<?php

namespace Leven\DBA\Common\BuilderPart;

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


    protected function whereGeneric(
        bool $isOr,
        string|callable $columnOrGroup,
        null|string|bool|int|float|array $valueOrOperand = [],
        null|string|bool|int|float|array $value = [],
        // array types because we need to allow use of null values
    ): static
    {
        if(is_callable($columnOrGroup)){
            $conditionBuilder = new WhereGroup($isOr);
            $columnOrGroup($conditionBuilder);
            $this->conditions[] = $conditionBuilder;
        }else{
            $this->conditions[] = ($value === []) ?
                new WhereCondition($isOr, $columnOrGroup, $valueOrOperand) :
                new WhereCondition($isOr, $columnOrGroup, $value, $valueOrOperand);
        }
        return $this;
    }

}