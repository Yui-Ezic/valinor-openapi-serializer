<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test;

readonly class NestedObject
{
    public function __construct(
        public int $id,
        public string $value,
    ) {}
}
