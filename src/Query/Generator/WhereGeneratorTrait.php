<?php

namespace Leven\DBA\MySQL\Query\Generator;

use Leven\DBA\Common\Part\WhereGroup;
use Leven\DBA\Common\Part\WhereCondition;
use Leven\DBA\Common\Part\WhereTrait;
use Leven\DBA\MySQL\Query;

trait WhereGeneratorTrait
{

    use WhereTrait;

    protected static function genQueryWhereRecursive(array $conditions): Query
    {
        $query = new Query;
        if(empty($conditions)) return $query;

        foreach ($conditions as $index => $condition) {
            if($index !== 0) $query->append($condition->isOr ? ' OR ' : ' AND ');

            if($condition instanceof WhereGroup) {
                $query->merge(static::genQueryWhereRecursive($condition->getConditions()));
            } else
                if($condition instanceof WhereCondition) {
                    $query->append(static::escapeName($condition->column) . ' ' . $condition->operand . ' ?');
                    $query->addParams($condition->value);
                }
        }

        return $query->wrap('(', ')');
    }

    protected function genQueryWhere(): Query
    {
        $conds = static::genQueryWhereRecursive($this->conditions);
        if($conds->empty()) return new Query;
        $conds->prepend(' WHERE ');
        return $conds;
    }

}