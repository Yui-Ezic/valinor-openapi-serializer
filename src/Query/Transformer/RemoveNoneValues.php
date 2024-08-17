<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer;

use YuiEzic\ValinorOpenapiSerializer\None;

/**
 * Remove keys that has None::class as value. Should be used with PreserveNoneObject::class
 *
 * @internal
 */
final class RemoveNoneValues
{
    /**
     * @psalm-suppress UnusedParam
     */
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (!is_array($result)) {
            return $result;
        }

        return array_filter($result, static function (mixed $value) {
            return !$value instanceof None;
        });
    }
}
