<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\Form;

use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\NestedObject;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form\ArrayNoExplode;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form\ObjectNoExplode;

readonly class NoExplode
{
    public function __construct(
        public int $int,
        public float $float,
        public string $string,
        /** @var list<string> */
        #[ArrayNoExplode]
        public array $stringList,
        #[ObjectNoExplode]
        public NestedObject $nestedObject,
    )
    {
    }
}
