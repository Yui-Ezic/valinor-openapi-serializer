<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\DeepObject;

use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\NestedObject;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\DeepObject\ObjectExplode;

readonly class Explode
{
    public function __construct(
        #[ObjectExplode(objectName: 'explode')]
        public NestedObject $nestedObject,
    )
    {
    }
}
