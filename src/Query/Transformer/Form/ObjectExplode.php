<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ExplodeValues;

/**
 * THIS TRANSFORMER MUST WORK IN TANDEM WITH YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues
 * TODO: find other method to do object explode, without YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues
 *
 * Transformer for plain object property with style=form, explode=true.
 * Converts Object id = {"role": "admin", "firstName": "Alex"}
 * to ?role=admin&firstName=Alex
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ObjectExplode
{
    /**
     * YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues search for EXPLODE_FLAG keys and explode it
     */
    public function normalizeKey(string $value): string
    {
        return ExplodeValues::EXPLODE_FLAG;
    }
}
