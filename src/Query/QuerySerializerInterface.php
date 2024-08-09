<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query;

interface QuerySerializerInterface
{
    /**
     * Serializing query object to string as defined in openapi specification.
     *
     * @see https://swagger.io/docs/specification/serialization/
     */
    public function serialize(object $query, bool $allowReserved = false): string;
}
