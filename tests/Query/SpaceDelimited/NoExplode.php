<?php

namespace Tests\Query\SpaceDelimited;

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
