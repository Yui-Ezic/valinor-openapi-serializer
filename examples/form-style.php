<?php

use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ArrayExplode;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

// Php objects that represents params
// Attributes are used to choose specific serialization style from openapi specification
readonly class QueryObject
{
    public function __construct(
        public int $int,
        public float $float,
        public string $string,
        /** @var list<string> */
        #[ArrayExplode('stringList')]
        public array $stringList,
        #[Form\ObjectExplode]
        public NestedObject $nestedObject,
    )
    {
    }
}

readonly class NestedObject
{
    public function __construct(
        public int $id,
        public string $value
    )
    {
    }
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



