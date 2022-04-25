<?php

namespace Leven\DBA\Common;

interface InsertQueryInterface
{

    public function execute(): AdapterResponse;

    public function set(array|string $dataOrColumn, null|string|bool|int|float $value = null): static;

}