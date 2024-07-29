<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\Form;

use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\NestedObject;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ArrayExplode;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form\ObjectExplode;

readonly class Explode
{
    public function __construct(
        public int $int,
        public float $float,
        public string $string,
        /** @var list<string> */
        #[ArrayExplode('stringList')]
        public array $stringList,
        #[ObjectExplode]
        public NestedObject $nestedObject,
    )
    {
    }
}
