<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\PipeDelimited;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;

use function YuiEzic\ValinorOpenapiSerializer\isArrayOfScalars;

/**
 * Transformer for array property with style=pipeDelimited, explode=false.
 * Converts Array id = [3, 4, 5]
 * to ?id=3|4|5
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ArrayNoExplode
{
    /**
     * @param non-empty-list $array
     */
    public function normalize(array $array): array|string
    {
        // Valinor does not support non-empty-list<scalar> so we need this
        if(!isArrayOfScalars($array)) {
            return $array;
        }
        return implode('|', $array);
    }
}
