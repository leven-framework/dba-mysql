<?php

namespace Leven\DBA\Common;

use Leven\DBA\Common\Exception\DriverException;
use Leven\DBA\Common\Exception\TxnNotActiveException;

interface AdapterInterface
{

    public function select(string $table): SelectQueryInterface;

    /**
     * @throws \Leven\DBA\Common\Exception\DriverException
     */
    public function insert(string $table, ?array $data = null): InsertQueryInterface|AdapterResponse;

    public function update(string $table): UpdateQueryInterface;

    public function delete(string $table): DeleteQueryInterface;

    /**
     * @throws \Leven\DBA\Common\Exception\DriverException
     */
    public function txnBegin(): static;

    /**
     * @throws \Leven\DBA\Common\Exception\TxnNotActiveException
     * @throws DriverException
     */
    public function txnCommit(): static;

    /**
     * @throws \Leven\DBA\Common\Exception\TxnNotActiveException
     * @throws DriverException
     */
    public function txnRollback(): static;
}