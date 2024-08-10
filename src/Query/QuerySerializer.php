<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query;

use RuntimeException;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ExplodeValues;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\UrlEncode;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;

use function YuiEzic\ValinorOpenapiSerializer\isArrayOfScalarsOrNulls;

final readonly class QuerySerializer implements QuerySerializerInterface
{
    /**
     * Serializing query object to string as defined in openapi specification.
     *
     * @see https://swagger.io/docs/specification/serialization/
     */
    public function serialize(object $query, bool $allowReserved = false): string
    {
        $mapperBuilder = (new MapperBuilder())
            ->registerTransformer(new UrlEncode($allowReserved))
            // For ObjectExplode attribute
            ->registerTransformer(new ExplodeValues());

        $array = $mapperBuilder->normalizer(Format::array())->normalize($query);

        if (!is_array($array) || !isArrayOfScalarsOrNulls($array)) {
            throw new RuntimeException('Invalid query object normalization, result is not an scalar array');
        }

        return self::toQueryString($array);
    }

    /**
     * @param array<scalar|null> $array
     */
    private static function toQueryString(array $array): string
    {
        $arrayForImplode = [];
        foreach ($array as $key => $value) {
            $stringValue = $value === null ? '' : (string) $value;
            $arrayForImplode[$key] = $key . '=' . $stringValue;
        }

        return implode("&", $arrayForImplode);
    }
}
