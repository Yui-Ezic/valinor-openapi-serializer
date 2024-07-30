<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\PipeDelimited;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;

/**
 * Transformer for array property with style=pipeDelimited, explode=false.
 * Converts Array id = [3, 4, 5]
 * to ?id=3|4|5
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
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
