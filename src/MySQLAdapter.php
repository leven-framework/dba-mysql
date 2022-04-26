<?php

namespace Leven\DBA\MySQL;

use Leven\DBA\Common\AdapterInterface;
use Leven\DBA\Common\AdapterResponse;
use Leven\DBA\Common\Exception\{DriverException, TxnNotActiveException};
use Leven\DBA\MySQL\Query\{
    DeleteQueryBuilder,
    DescribeQueryBuilder,
    InsertQueryBuilder,
    SelectQueryBuilder,
    UpdateQueryBuilder
};
use PDO, PDOException;

class MySQLAdapter implements AdapterInterface
{

    protected PDO $driver;
    protected int $txnDepth = 0;

    /**
     * @throws DriverException
     */
    function __construct(
        string|PDO $database,
        string     $user = '',
        string     $password = '',
        string     $host = '127.0.0.1',
        int        $port = 3306,
        string     $charset = 'UTF8',
        public     readonly string $tablePrefix = '',
    )
    {
        if ($database instanceof PDO) {
            $this->driver = $database;
            return;
        }

        $args = [
            "dsn" => "mysql:host=$host;" .
                "port=$port;" .
                "dbname=$database;" .
                "charset=$charset",
            "username" => $user,
            "password" => $password,
            "options" => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        ];

        try { $this->driver = new PDO(...$args); }
        catch (PDOException $e) {
            throw new DriverException(message: 'PDO init failed', previous: $e);
        }
    }

    /**
     * @throws DriverException
     */
    public function executeQuery(Query $query): AdapterResponse
    {
        try {
            $stat = $this->driver->prepare($query->getQuery());
            $stat->execute($query->getParams());
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }

        return new AdapterResponse(
            $stat->rowCount(),
            $stat->fetchAll(),
            $this->driver->lastInsertId()
        );
    }


    // QUERY BUILDERS

    public function describe(string $table): DescribeQueryBuilder
    {
        return new DescribeQueryBuilder($table, $this);
    }

    public function select(string $table): SelectQueryBuilder
    {
        return new SelectQueryBuilder($table, $this);
    }

    // alias for select
    public function get(string $table): SelectQueryBuilder
    {
        return $this->select($table);
    }

    /**
     * @throws DriverException
     */
    public function insert(string $table, ?array $data = null): InsertQueryBuilder|AdapterResponse
    {
        $builder = new InsertQueryBuilder($table, $this);

        if ($data === null) return $builder;

        $builder->set($data);
        return $builder->execute();
    }

    public function update(string $table): UpdateQueryBuilder
    {
        return new UpdateQueryBuilder($table, $this);
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        return new DeleteQueryBuilder($table, $this);
    }


    // TRANSACTIONS

    /**
     * @throws \Leven\DBA\Common\Exception\DriverException
     */
    public function txnBegin(): static
    {
        if ($this->txnDepth++ !== 0) return $this;

        try { $this->driver->beginTransaction(); }
        catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnCommit(): static
    {
        if ($this->txnDepth === 0) throw new TxnNotActiveException;

        try { if (--$this->txnDepth === 0) $this->driver->commit(); }
        catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnRollback(): static
    {
        if ($this->txnDepth === 0) throw new TxnNotActiveException;

        $this->txnDepth = 0;
        try { $this->driver->rollBack(); }
        catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

}
