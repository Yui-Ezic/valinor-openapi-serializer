<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test;

use CuyZ\Valinor\MapperBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\DeepObject;
use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\Form;
use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\PipeDelimited;
use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\SpaceDelimited;
use YuiEzic\ValinorOpenapiSerializer\Query\Test\QueryObject\StringValue;

class QuerySerializerTest extends TestCase
{
    public static function dataProvider(): array
    {
        $reserved = "/?#[]@!$&'()*+,;=";
        $shouldBeEncoded = ' |^ёі';
        $unreserved = '-._~' . 'Ab09';

        return [
            'allowReserved=true encode everything except reserved' => [
                'query' => new StringValue($unreserved . $reserved . $shouldBeEncoded),
                'allowReserved' => true,
                'expected' => 'value=' . $unreserved . $reserved . rawurlencode($shouldBeEncoded)
            ],
            'allowReserved=false encode everything' => [
                'query' => new StringValue($unreserved . $reserved . $shouldBeEncoded),
                'allowReserved' => false,
                'expected' => 'value=' . $unreserved . rawurlencode($reserved . $shouldBeEncoded)
            ],
            'Form, explode, no allow reserved' => [
                'query' => Form\Explode::class,
                'allowReserved' => false,
                'expected' => implode('&', [
                    'int=3',
                    'float=3.14',
                    'string=' . 'hello' . '%20' . 'world',
                    'stringList=first&stringList=second',
                    'id=1',
                    'value=foo'
                ]),
            ],
            'Form, explode, allow reserved' => [
                'query' => Form\Explode::class,
                'allowReserved' => true,
                'expected' => implode('&', [
                    'int=3',
                    'float=3.14',
                    'string=' . 'hello' . '%20' . 'world',
                    'stringList=first&stringList=second',
                    'id=1',
                    'value=foo'
                ]),
            ],
            'Form, no explode, no allow reserved' => [
                'query' => Form\NoExplode::class,
                'allowReserved' => false,
                'expected' => implode('&', [
                    'int=3',
                    'float=3.14',
                    'string=' . 'hello' . '%20' . 'world',
                    'stringList=first,second',
                    'nestedObject=id,1,value,foo',
                ]),
            ],
            'Form, no explode, allow reserved' => [
                'query' => Form\NoExplode::class,
                'allowReserved' => true,
                'expected' => implode('&', [
                    'int=3',
                    'float=3.14',
                    'string=' . 'hello' . '%20' . 'world',
                    'stringList=first,second',
                    'nestedObject=id,1,value,foo',
                ]),
            ],
            'SpaceDelimited, no explode, no allow reserved' => [
                'query' => SpaceDelimited\NoExplode::class,
                'allowReserved' => false,
                'expected' => implode('&', [
                    'stringList=' . 'first' . '%20' . 'second',
                ]),
            ],
            'SpaceDelimited, no explode, allow reserved' => [
                'query' => SpaceDelimited\NoExplode::class,
                'allowReserved' => true,
                'expected' => implode('&', [
                    'stringList=' . 'first' . '%20' . 'second',
                ]),
            ],
            'PipeDelimited, no explode, no allow reserved' => [
                'query' => PipeDelimited\NoExplode::class,
                'allowReserved' => false,
                'expected' => implode('&', [
                    'stringList=' . 'first' . '|' . 'second',
                ]),
            ],
            'PipeDelimited, no explode, allow reserved' => [
                'query' => PipeDelimited\NoExplode::class,
                'allowReserved' => true,
                'expected' => implode('&', [
                    'stringList=' . 'first' . '|' . 'second',
                ]),
            ],
            'DeepObject, explode, no allow reserved' => [
                'query' => DeepObject\Explode::class,
                'allowReserved' => false,
                'expected' => implode('&', [
                    'explode[id]=1',
                    'explode[value]=foo',
                ]),
            ],
            'DeepObject, explode, allow reserved' => [
                'query' => DeepObject\Explode::class,
                'allowReserved' => true,
                'expected' => implode('&', [
                    'explode[id]=1',
                    'explode[value]=foo',
                ]),
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testSerialize(string|object $query, bool $allowReserved, string $expected): void
    {
        if (is_string($query)) {
            $queryArray = [
                'int' => 3,
                'float' => 3.14,
                'string' => 'hello world',
                'stringList' => ['first', 'second'],
                'nestedObject' => [
                    'id' => 1,
                    'value' => 'foo'
                ]
            ];

            $query = (new MapperBuilder())
                ->allowSuperfluousKeys()
                ->mapper()
                ->map($query, $queryArray);
        }

        $actual = (new QuerySerializer())->serialize(
            query: $query,
            allowReserved: $allowReserved,
        );

        self::assertEquals($expected, $actual);
    }
}
