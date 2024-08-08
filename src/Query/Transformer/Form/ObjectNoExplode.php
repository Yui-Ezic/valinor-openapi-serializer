<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;
use RuntimeException;
use Stringable;

/**
 * Transformer for plain object property with style=form, explode=false.
 * Converts Object id = {"role": "admin", "firstName": "Alex"}
 * to ?id=role,admin,firstName,Alex
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ObjectNoExplode
{
    /**
     * @psalm-suppress UnusedParam
     */
    public function normalize(object $object, callable $next): mixed
    {
        $result = $next();

        if (!is_array($result)) {
            return $result;
        }

        $newValue = '';
        foreach ($result as $key => $value) {
            if (!is_scalar($value)) {
                throw new RuntimeException('Query serialization supports only plain objects.');
            }
            $newValue .= $key . ',' . $value . ',';
        }
        return substr($newValue, 0, -1);
    }
}
