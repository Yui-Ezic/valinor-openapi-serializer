<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query;

use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ExplodeValues;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\UrlEncode;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;

/**
 * Serializing query object to string as defined in openapi specification.
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
final readonly class QuerySerializer
{
    public function serialize(object $query, bool $allowReserved = false): string
    {
        $mapperBuilder = new MapperBuilder();

        $mapperBuilder = $mapperBuilder
            ->registerTransformer(new UrlEncode($allowReserved))
            // For ObjectExplode attribute
            ->registerTransformer(new ExplodeValues());

        $array = $mapperBuilder->normalizer(Format::array())->normalize($query);

        $arrayForImplode = [];
        foreach ($array as $key => $value) {
            $arrayForImplode[$key] = $key . '=' . $value;
        }

        return implode("&", $arrayForImplode);
    }
}
