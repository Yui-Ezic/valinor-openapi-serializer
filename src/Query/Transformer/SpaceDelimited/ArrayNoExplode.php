<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\SpaceDelimited;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;

use RuntimeException;

use function YuiEzic\ValinorOpenapiSerializer\isArrayOfScalars;

/**
 * Transformer for array property with style=pipeDelimited, explode=false.
 * Converts Array id = [3, 4, 5]
 * to ?id=3%204%205 (values delimited by '%20')
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayNoExplode
{
    private const string ENCODED_SPACE = '%20';

    /**
     * @param non-empty-list $array
     */
    public function normalize(array $array): array|string
    {
        // Valinor does not support non-empty-list<scalar> so we need this
        if(!isArrayOfScalars($array)) {
            return $array;
        }
        return implode(self::ENCODED_SPACE, $array);
    }
}
