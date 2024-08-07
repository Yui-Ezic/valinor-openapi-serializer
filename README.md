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

| style          | explode | 	URI template | Primitive value id = 5 | Array id = [3, 4, 5]  | Object id = {"role": "admin", "firstName": "Alex"} |
|----------------|---------|---------------|------------------------|-----------------------|----------------------------------------------------|
| form           | true    | /users{?id*}  | /users?id=5            | /users?id=3&id=4&id=5 | 	/users?role=admin&firstName=Alex                  |
| form           | false   | /users{?id}   | /users?id=5            | 	/users?id=3,4,5      | /users?id=role,admin,firstName,Alex                |
| spaceDelimited | true    | /users{?id*}  | n/a                    | /users?id=3&id=4&id=5 | n/a                                                |
| spaceDelimited | false   | n/a           | n/a                    | /users?id=3%204%205   | 	n/a                                               |
| pipeDelimited  | true    | /users{?id*}  | n/a                    | /users?id=3&id=4&id=5 | 	n/a                                               |
| pipeDelimited  | false   | n/a           | n/a                    | /users?id=3\|4\|5     | 	n/a                                               |
| deepObject     | true    | n/a           | n/a                    | n/a                   | 	/users?id[role]=admin&id[firstName]=Alex          |

### Example of query params serialization

```php
<?php

use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

require 'vendor/autoload.php';

// Php objects that represents params
// Attributes are used to choose specific serialization style for arrays and object from openapi specification
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

For more examples you can check [tests](src/Query/Test/QuerySerializerTest.php)
