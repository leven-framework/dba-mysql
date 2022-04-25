<?php

namespace Leven\DBA\Common;

interface DeleteQueryInterface
{

    public function execute(): AdapterResponse;

    public function limit(int $limitOrOffset, int $limit = 0): static;

    public function offset(int $offset): static;

    public function orderAsc(string $column): static;

    public function orderDesc(string $column): static;

    public function andWhere(string|callable $columnOrGroup, null|string|bool|int|float|array $valueOrOperand = [], null|string|bool|int|float|array $value = []): static;

    public function orWhere(string|callable $columnOrGroup, null|string|bool|int|float|array $valueOrOperand = [], null|string|bool|int|float|array $value = []): static;

    public function where(string|callable $columnOrGroup, null|string|bool|int|float|array $valueOrOperand = [], null|string|bool|int|float|array $value = []): static;

}