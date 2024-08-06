<?php

declare(strict_types=1);

namespace YuiEzic\ValinorOpenapiSerializer;

/**
 * @psalm-assert-if-true array<scalar> $array
 */
function isArrayOfScalars(array $array): bool
{
    foreach ($array as $value) {
        if (!is_scalar($value)) {
            return false;
        }
    }
    return true;
}
