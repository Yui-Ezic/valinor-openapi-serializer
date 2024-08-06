<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;

use function YuiEzic\ValinorOpenapiSerializer\isArrayOfScalars;

/**
 * Array serialization with explode=true is exactly same for all styles, so this class on general namespace
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ArrayExplode
{
    public function __construct(
        // TODO: Stupid hack because idk how to get key value in normalize() method.
        private string $key,
        private string $delimiter = '&',
    ) {}

    /**
     * @param non-empty-list $array
     */
    public function normalize(array $array): array|string
    {
        // Valinor does not support non-empty-list<scalar> so we need this
        if(!isArrayOfScalars($array)) {
            return $array;
        }

        $result = null;

        foreach ($array as $value) {
            if ($result === null) {
                // Skip key in first value, cause this key will be added by normalizer
                $result = (string) $value;
            } else {
                $result .= $this->delimiter . $this->key . '=' . $value;
            }
        }

        return $result;
    }
}
