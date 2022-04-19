<?php

namespace Leven\DBA\MySQL\Query;

use Leven\DBA\Common\Exception\Driver\DriverException;
use Leven\DBA\MySQL\DatabaseAdapterResponse;
use Leven\DBA\MySQL\MySQLAdapter;
use Leven\DBA\MySQL\Query;

abstract class BaseQueryBuilder
{

    public function __construct(
        protected readonly string $table,
        protected readonly ?MySQLAdapter $adapter = null,
    )
    {
    }

    abstract public function getQuery(): Query;

    /**
     * @throws DriverException
     */
    final public function execute(): DatabaseAdapterResponse
    {
        if($this->adapter === null)
            throw new \Exception('pass the built query to the adapter executeQuery() method');

        return $this->adapter->executeQuery($this->getQuery());
    }

    // INTERNAL

    final protected static function escapeName(string $string): string
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }

    final protected function genQueryTable(): Query
    {
        return new Query(static::escapeName($this->table));
    }

}