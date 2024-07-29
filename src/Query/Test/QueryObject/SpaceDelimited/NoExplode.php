<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\SpaceDelimited;

use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\SpaceDelimited\ArrayNoExplode;

readonly class NoExplode
{
    public function __construct(
        /** @var list<string> */
        #[ArrayNoExplode]
        public array $stringList,
    )
    {
    }
}
