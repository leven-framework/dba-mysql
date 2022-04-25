<?php namespace Leven\DBA\Mock;
// by Leon, MIT License

use Closure;
use Leven\DBA\Common\AdapterInterface;
use Leven\DBA\Common\AdapterResponse;
use Leven\DBA\Common\Exception\Driver\TxnNotActiveException;
use Leven\DBA\Mock\Query\DeleteQueryBuilder;
use Leven\DBA\Mock\Query\InsertQueryBuilder;
use Leven\DBA\Mock\Query\SelectQueryBuilder;
use Leven\DBA\Mock\Query\UpdateQueryBuilder;
use Leven\DBA\Mock\Structure\Database;

class MockAdapter implements AdapterInterface
{

    protected Database $database;

    protected int $txnDepth = 0;
    protected array $txnDbRollback;

    public function __construct(
        array|Closure      $restore = [],
        protected ?Closure $onUpdate = null,

        protected string   $prefix = '',
    )
    {
        if(is_callable($restore)) $restore = $restore();

        $this->database = Database::fromArray($restore);
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function executeQuery(Query $query): AdapterResponse
    {
        if(is_callable($query->update)) ($query->update)($this);

        return new AdapterResponse($query->count, $query->rows);
    }

    public function save()
    {
        if(is_callable($this->onUpdate) && $this->txnDepth === 0)
            ($this->onUpdate)($this->database->toArray());
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
        if($this->txnDepth++ === 0)
            $this->txnDbRollback = $this->database->toArray();

        return $this;
    }

    public function txnCommit(): static
    {
        if($this->txnDepth === 0) throw new TxnNotActiveException;
        if(--$this->txnDepth > 0) return $this; // still in txn

        unset($this->txnDbRollback);
        $this->save();

        return $this;
    }

    public function txnRollback(): static
    {
        if($this->txnDepth === 0) throw new TxnNotActiveException;

        $this->txnDepth = 0;
        $this->database = Database::fromArray($this->txnDbRollback);

        return $this;
    }
}
