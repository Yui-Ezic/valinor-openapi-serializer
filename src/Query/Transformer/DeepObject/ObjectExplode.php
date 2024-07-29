<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer\DeepObject;

use Attribute;
use CuyZ\Valinor\Normalizer\AsTransformer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ExplodeValues;

/**
 * THIS TRANSFORMER MUST WORK IN TANDEM WITH YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues
 * TODO: find other method to do object explode, without YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues
 */
#[AsTransformer]
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ObjectExplode
{
    public function __construct(
        private string $objectName
    )
    {
    }

    /**
     * YuiEzic\ValinorOpenapiSerializer\QuerySerializer\Transformer\ExplodeValues search for EXPLODE_FLAG keys and explode it
     */
    public function normalizeKey(string $value): string
    {
        return ExplodeValues::EXPLODE_FLAG;
    }

    public function normalize(object $object, callable $next): array
    {
        $array = $next();

        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $this->objectName . "[$key]";
            $result[$newKey] = $value;
        }
        return $result;
    }
}
