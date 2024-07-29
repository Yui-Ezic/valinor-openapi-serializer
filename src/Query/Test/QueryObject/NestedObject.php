<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject;

readonly class NestedObject
{
    public function __construct(
        public int $id,
        public string $value
    )
    {
    }
}
