<?php

namespace Leven\DBA\Mock\Query\Filter;

use Leven\DBA\Common\Part\WhereCondition;
use Leven\DBA\Common\Part\WhereGroup;
use Leven\DBA\Common\Part\WhereTrait;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

trait WhereFilterTrait
{

    use WhereTrait;

    protected static function genWhereExpression(array $conditions): string
    {
        if(empty($conditions)) return '';
        $exp = '(';

        foreach ($conditions as $index => $condition) {
            if($index !== 0) $exp .= $condition->isOr ? ' || ' : ' && ';

            if($condition instanceof WhereGroup) {
                $exp .= static::genWhereExpression($condition->getConditions());
                continue;
            }

            assert($condition instanceof WhereCondition);

            $value = $condition->value;
            if(is_string($value)) $value = json_encode($value); // quote and escape string
            else if(is_bool($value)) $value = $value ? 'true' : 'false'; // convert boolean to string
            else if(is_null($value)) $value = 'null'; // convert null to string

            $operand = $condition->operand;
            if($operand === '<=>' || $operand === '===') $operand = '=='; // achieve more mysql-like behavior

            $exp .= "$condition->column $operand $value";
        }

        return "$exp)";
    }

    protected function filterWhere(): static
    {
        $exp = static::genWhereExpression($this->conditions);
        if($exp === '') return $this;

        $el = new ExpressionLanguage;
        $columns = array_keys($this->workset[0]);
        foreach($this->workset as $index => $row){
            if($index === 0) continue; // column names

            $result = $el->evaluate($exp, array_combine($columns, $row));
            if($result === false) unset($this->workset[$index]);
        }

        return $this;
    }

}