<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\PipeDelimited;

use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\PipeDelimited\ArrayNoExplode;

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
