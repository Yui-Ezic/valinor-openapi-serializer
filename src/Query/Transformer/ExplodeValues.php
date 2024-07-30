<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer;

/**
 * Class to realize object or array explosion to higher level.
 *
 * This transformer is added to normalizer in YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer
 *
 * REQUIRED for the following transformers to work:
 * - YuiEzic\ValinorOpenapiSerializer\Query\Transformer\DeepObject\ObjectExplode::class
 * - YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form\ObjectExplode::class
 *
 *
 * ---------------------------------------------------
 * The rationale for the existence of this transformer
 * ---------------------------------------------------
 *
 * For example, serialization for Object id = {"role": "admin", "firstName": "Alex"} property,
 * looks like "?role=admin&firstName=Alex", where we should generate separate query parameter for
 * each object property.
 *
 * But valinor property transformer cannot transform one value to different keys.
 * And result will be like "?id=" . "role=admin&firstName=Alex", with an extra 'id' key, where 'id'
 * is name of property. To remove 'id' key, another transformer change that key to predefined
 * '-explode' value, and then this transformer, which applies to whole object, remove this keys,
 * and explode values to higher level.
 *
 * Another alternative is to do transformer for whole class that takes names of properties, that
 * should be exploded. And this transformer will do the explosion itself. This is a more explicit
 * solution. but less user-friendly. Because user should give to transformer all properties names,
 * instead of just wrote attribute for properties to explode. In example:
 *
 * [#ExplodeObjects(['nestedObject'])]
 * readonly class Query {
 *     public int $id;
 *     public NestedObject $nestedObject;
 * }
 *
 * Instead of
 *
 * readonly class Query {
 *     public int $id;
 *     [#ObjectExplode]
 *     public NestedObject $nestedObject;
 * }
 *
 * The second variants seems better for me, despite the implicit addition of a ExplodeValue transformer
 * necessary for proper normalization. But I'm still not sure.
 */
class ExplodeValues
{
    // Begins with '-' because real properties can't start with it
    public const string EXPLODE_FLAG = '-explode';

    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (!is_array($result)) {
            return $result;
        }

        $transformed = [];
        foreach ($result as $key => $value) {
            if ($key === self::EXPLODE_FLAG) {
                foreach ($value as $k => $v) {
                    $transformed[$k] = $v;
                }
                continue;
            }

            $transformed[$key] = $value;
        }
        return $transformed;
    }
}
