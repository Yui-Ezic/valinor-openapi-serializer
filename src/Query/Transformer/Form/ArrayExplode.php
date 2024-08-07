<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\AbstractArrayExplode;

/**
 * Transformer for array property with style=form, explode=true.
 * Converts Array id = [3, 4, 5]
 * to ?id=3&id=4&id=5
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ArrayExplode extends AbstractArrayExplode {}
