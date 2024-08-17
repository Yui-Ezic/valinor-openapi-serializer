<?php

namespace YuiEzic\ValinorOpenapiSerializer;

/**
 * Represents the absence of property. When no property is present.
 * Do not confuse with null when the property exists, but has no value.
 *
 * Examples of serializing to json:
 * $object->value = new None() serializing to {}
 * $object->value = null serializing to {"value": null}
 */
final class None {}
