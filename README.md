# Valinor openapi serializer

Serialize/Deserialize parameters from/to objects
by [openapi specification](https://swagger.io/docs/specification/serialization/)
with valinor

## Implemented features

| Parameter type | Serialization | Deserialization |
|----------------|---------------|-----------------|
| Query          | ✅             | ❌               |
| Path           | ❌             | ❌               |
| Header         | ❌             | ❌               |
| Cookie         | ❌             | ❌               |

## Query params serialization

### Table of serialization methods

| style          | explode | Primitive id = 5 | Array id = [3, 4, 5]  | Object id = {"role": "user", "name": "Leo"} |
|----------------|---------|------------------|-----------------------|---------------------------------------------|
| form           | true    | /users?id=5      | /users?id=3&id=4&id=5 | /users?role=user&name=Leo                   |
| form           | false   | /users?id=5      | /users?id=3,4,5       | /users?id=role,user,name,Leo                |
| spaceDelimited | true    | n/a              | /users?id=3&id=4&id=5 | n/a                                         |
| spaceDelimited | false   | n/a              | /users?id=3%204%205   | n/a                                         |
| pipeDelimited  | true    | n/a              | /users?id=3&id=4&id=5 | n/a                                         |
| pipeDelimited  | false   | n/a              | /users?id=3\|4\|5     | n/a                                         |
| deepObject     | true    | n/a              | n/a                   | /users?id[role]=user&id[name]=Leo           |

### Example of query params serialization

```php
<?php

use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

require 'vendor/autoload.php';

// Php objects that represents params
// Attributes are used to choose serialization style for arrays and object from openapi specification
readonly class QueryObject
{
    public function __construct(
        public int $int,
        public float $float,
        public string $string,
        /** @var list<string> */
        #[Form\ArrayExplode('stringList')]
        public array $stringList,
        #[Form\ObjectExplode]
        public NestedObject $nestedObject,
    ) {}
}

readonly class NestedObject
{
    public function __construct(
        public int $id,
        public string $value
    ) {}
}

// Serialize php object to query string
$queryString = (new QuerySerializer())->serialize(
    query: new QueryObject(
        int: 3,
        float: 3.14,
        string: 'hello world',
        stringList: ['first', 'second'],
        nestedObject: new NestedObject(id: 1, value: 'foo'),
    ),
    allowReserved: false,
);

// int=3&float=3.14&string=hello%20world&stringList=first&stringList=second&id=1&value=foo
echo $queryString . PHP_EOL;
```

### Not existent (optional) fields

```php
<?php

use YuiEzic\ValinorOpenapiSerializer\None;
use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

require 'vendor/autoload.php';

// You can use the None::class to represent absence of property (field)
readonly class QueryObject
{
    public function __construct(
        public int|None $foo,
        public int|null $bar
    ) {}
}

// Serialize php object to query string
$queryString = (new QuerySerializer())->serialize(
    query: new QueryObject(foo: new None(), bar: null)
);

// bar=
echo $queryString . PHP_EOL;
```

For more examples you can check [tests](src/Query/Test/QuerySerializerTest.php)

### (De-)Serializing null, required, and empty values

Property can be in next states: not present, null, empty value, has value.
But [RFC 6570](https://datatracker.ietf.org/doc/html/rfc6570)
which openapi use for serializing query parameters has no support of all this states. The problem is well described
here [(De-)Serializing null, required, and empty values ins OAS parameters #2037](https://github.com/OAI/OpenAPI-Specification/issues/2037)

In short, we cannot distinguish a null value from an empty one. Look at table below:

| 	                                | \<no prop\> | prop: null | 	prop: '' | 	prop: 'a' |
|----------------------------------|-------------|------------|-----------|------------|
| required=false<br>nullable=false | INVALID     | INVALID    | prop=     | prop=a     |
| required=true<br>nullable=false  |             | INVALID    | prop=     | prop=a     |
| required=false<br>nullable=true  | INVALID     | prop=      | prop=     | prop=a     |
| required=true<br>nullable=true   |             | prop=      | prop=     | prop=a     |

As you can see serialization of null and '' is same in some cases, and we won't be able to deserialize the value
unambiguously. To represent null we need to add some constraints. For example, impose a constraint on a string that it
cannot be empty.

I don't take on the problem with strings, but I try to support null values in cases where there is no ambiguity.

#### Rules of null serializing:

1. If property type is string than null value is not allowed.
2. If property type is not string (int, float, array, object) than empty string is used to represent null value (e.g '
   prop=')
3. Array should not have null items. Because we can't tell whether `prop=1,3` is `[1, null, 3]` or `[1, 3]`.
4. Nested objects should not have nullable properties. `{"object": {id: 1, value:null}}` is not allowed. Root query
   object can have.
