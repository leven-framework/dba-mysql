<?php namespace Leven\DBA\Mock;
// by Leon, MIT License

use Leven\DBA\Common\AdapterInterface;
use Leven\DBA\Common\AdapterResponse;
use Leven\DBA\Mock\Query\DeleteQueryBuilder;
use Leven\DBA\Mock\Query\InsertQueryBuilder;
use Leven\DBA\Mock\Query\SelectQueryBuilder;
use Leven\DBA\Mock\Query\UpdateQueryBuilder;
use Leven\DBA\Mock\Structure\Database;

class MockAdapter implements AdapterInterface
{

    protected int $txnDepth = 0;

    public function __construct(
        protected Database $store = new Database,
        protected string   $prefix = '',
    )
    {
    }

    public function getStore(): Database
    {
        return $this->store;
    }

    public function executeQuery(Query $query): AdapterResponse
    {
        if(is_callable($query->update)) ($query->update)($this->store);

        return new AdapterResponse($query->count, $query->rows);
    }


    // QUERY BUILDERS

    public function select(string $table): SelectQueryBuilder
    {
        return new SelectQueryBuilder($this, $this->prefix . $table);
    }

    public function insert(string $table, ?array $data = null): InsertQueryBuilder|AdapterResponse
    {
        $builder = new InsertQueryBuilder($this, $this->prefix . $table);

        if($data === null) return $builder;

        $builder->set($data);
        return $builder->execute();
    }

    public function update(string $table): UpdateQueryBuilder
    {
        return new UpdateQueryBuilder($this, $this->prefix . $table);
    }

    public function delete(string $table): DeleteQueryBuilder
    {
        return new DeleteQueryBuilder($this, $this->prefix . $table);
    }


    // TRANSACTIONS

    public function txnBegin(): static
    {
        // TODO: Implement txnBegin() method.
    }

    public function txnCommit(): static
    {
        // TODO: Implement txnCommit() method.
    }

    public function txnRollback(): static
    {
        // TODO: Implement txnRollback() method.
    }
}
