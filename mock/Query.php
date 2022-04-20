<?php

namespace Leven\DBA\Mock;

class Query
{

    public function __construct(
        public readonly array $resultRows,
        public readonly int $resultCount,
        public readonly ?array $storeUpdate = null,
    )
    {
    }

}