<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\PipeDelimited;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;

#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayNoExplode
{
    /**
     * @param non-empty-list $array
     */
    public function normalize(array $array, callable $next): string
    {
        return implode('|', $array);
    }
}
