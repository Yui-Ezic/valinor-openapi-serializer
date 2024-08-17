<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer;

use YuiEzic\ValinorOpenapiSerializer\None;

/**
 * Keep None::class as class instead of converting it to array, to make RemoveNoneValues::class work
 *
 * @internal
 */
final readonly class PreserveNoneObject
{
    /**
     * @psalm-suppress UnusedParam
     */
    public function __invoke(None $object, callable $next): None
    {
        return $object;
    }
}
