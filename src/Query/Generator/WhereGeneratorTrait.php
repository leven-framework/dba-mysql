<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\BuilderPart\{WhereGroup, WhereCondition, WhereTrait};
use Leven\DBA\MySQL\Query;

trait WhereGeneratorTrait
{

    use WhereTrait;

    protected static function genQueryWhereRecursive(array $conditions): Query
    {
        $query = new Query;
        if(empty($conditions)) return $query;

        foreach ($conditions as $condition) {
            if(!$query->empty()) $query->append($condition->isOr ? ' OR ' : ' AND ');

            if($condition instanceof WhereGroup) {
                $query->merge(static::genQueryWhereRecursive($condition->getConditions()));
            } else
            if($condition instanceof WhereCondition) {
                if(is_array($condition->value)){
                    $ph = implode(',', array_fill(0, count($condition->value), '?'));
                    $query->append(static::escapeName($condition->column) . ' ' . $condition->operand . " ($ph)");
                    $query->addParams(...$condition->value);
                    continue;
                }

                $query->append(static::escapeName($condition->column) . ' ' . $condition->operand . ' ?');
                $query->addParams($condition->value);
            }
        }

        return $query->empty() ? $query : $query->wrap('(', ')');
    }

    protected function genQueryWhere(): Query
    {
        $query = static::genQueryWhereRecursive($this->conditions);
        return $query->empty() ? $query : $query->prepend(' WHERE ');
    }

}