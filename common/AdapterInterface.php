<?php

namespace Leven\DBA\Common;

use Leven\DBA\Common\Exception\Driver\DriverException;
use Leven\DBA\Common\Exception\Driver\TxnNotActiveException;

interface AdapterInterface
{

    public function select(string $table): SelectQueryInterface;

    /**
     * @throws DriverException
     */
    public function insert(string $table, ?array $data = null): InsertQueryInterface|AdapterResponse;

    public function update(string $table): UpdateQueryInterface;

    public function delete(string $table): DeleteQueryInterface;

    /**
     * @throws DriverException
     */
    public function txnBegin(): static;

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnCommit(): static;

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnRollback(): static;
}