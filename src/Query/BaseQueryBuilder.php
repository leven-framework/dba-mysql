<?php

namespace Leven\DBA\MySQL\Query;

use BadMethodCallException;
use Leven\DBA\Common\AdapterResponse;
use Leven\DBA\Common\Exception\DriverException;
use Leven\DBA\MySQL\MySQLAdapter;
use Leven\DBA\MySQL\Query;

abstract class BaseQueryBuilder
{

    public function __construct(
        protected readonly string        $table,
        protected readonly ?MySQLAdapter $adapter = null,
    )
    {
    }

    abstract public function getQuery(): Query;

    /**
     * @throws DriverException
     */
    final public function execute(): AdapterResponse
    {
        if($this->adapter === null){
            $msg = 'pass the built query to adapters executeQuery() method OR initialize the builder through the adapter';
            throw new BadMethodCallException($msg);
        }

        return $this->adapter->executeQuery($this->getQuery());
    }

    // INTERNAL

    final protected static function escapeName(string $string): string
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }

    final protected function genQueryTable(): Query
    {
        return new Query(static::escapeName($this->adapter->tablePrefix . $this->table));
    }

}