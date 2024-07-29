<?php

namespace Tests\Query;

readonly class NestedObject
{
    public function __construct(
        public int $id,
        public string $value
    )
    {
    }
}
