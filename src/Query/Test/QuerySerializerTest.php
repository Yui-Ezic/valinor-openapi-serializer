<?php

declare(strict_types=1);

namespace YuiEzic\ValinorOpenapiSerializer\Query\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use YuiEzic\ValinorOpenapiSerializer\None;
use YuiEzic\ValinorOpenapiSerializer\Query\QuerySerializer;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\DeepObject\ObjectExplode;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\Form;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\PipeDelimited;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\SpaceDelimited;

final class QuerySerializerTest extends TestCase
{
    public static function dataProvider(): array
    {
        // RFC3986 char sets
        $reserved = "/?#[]@!$&'()*+,;=";
        $disallowed = ' |^ёі';
        $unreserved = '-._~' . 'Ab09';

        return [
            // Test reserved chars encoding
            'allowReserved=true does not encode reserved' => [
                'query' => self::stringQueryObject($reserved),
                'allowReserved' => true,
                'expected' => 'value=' . $reserved,
            ],
            'allowReserved=false encodes reserved' => [
                'query' => self::stringQueryObject($reserved),
                'allowReserved' => false,
                'expected' => 'value=' . rawurlencode($reserved),
            ],

            // Test disallowed chars encoding
            'allowReserved=true encodes not reserved disallowed chars' => [
                'query' => self::stringQueryObject($disallowed),
                'allowReserved' => true,
                'expected' => 'value=' . rawurlencode($disallowed),
            ],
            'allowReserved=false encodes not reserved disallowed chars' => [
                'query' => self::stringQueryObject($disallowed),
                'allowReserved' => false,
                'expected' => 'value=' . rawurlencode($disallowed),
            ],

            // Test unreserved chars encoding
            'allowReserved=true does not encode unreserved' => [
                'query' => self::stringQueryObject($unreserved),
                'allowReserved' => true,
                'expected' => 'value=' . $unreserved,
            ],
            'allowReserved=false does not encode unreserved' => [
                'query' => self::stringQueryObject($unreserved),
                'allowReserved' => false,
                'expected' => 'value=' . $unreserved,
            ],

            // Test simple types serializing
            'int' => [
                'query' => self::intQueryObject(3),
                'allowReserved' => false,
                'expected' => 'value=3',
            ],
            'float' => [
                'query' => self::floatQueryObject(3.14),
                'allowReserved' => false,
                'expected' => 'value=3.14',
            ],

            // Form style array serializing
            'array, Form, explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[Form\ArrayExplode('value')]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first&value=second',
            ],
            'array, Form, no explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[Form\ArrayNoExplode()]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first,second',
            ],

            // Form style object serializing
            'object, Form, explode, no allow reserved' => [
                'query' => new class (new NestedObject(id: 1, value: 'foo')) {
                    public function __construct(
                        #[Form\ObjectExplode]
                        public NestedObject $object,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'id=1&value=foo',
            ],
            'object, Form, no explode, no allow reserved' => [
                'query' => new class (new NestedObject(id: 1, value: 'foo')) {
                    public function __construct(
                        #[Form\ObjectNoExplode]
                        public NestedObject $object,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'object=id,1,value,foo',
            ],

            // SpaceDelimited style array serialization
            'array, SpaceDelimited, explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[SpaceDelimited\ArrayExplode('value')]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first&value=second',
            ],
            'array, SpaceDelimited, no explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[SpaceDelimited\ArrayNoExplode]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first' . '%20' . 'second',
            ],

            // PipeDelimited style array serialization
            'array, PipeDelimited, explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[PipeDelimited\ArrayExplode('value')]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first&value=second',
            ],
            'array, PipeDelimited, no explode, no allow reserved' => [
                'query' => new class (['first', 'second']) {
                    public function __construct(
                        /** @var list<string> */
                        #[PipeDelimited\ArrayNoExplode]
                        public array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=first|second',
            ],

            // DeepObject style object serialization
            'object, DeepObject, explode, no allow reserved' => [
                'query' => new class (new NestedObject(id: 1, value: 'foo')) {
                    public function __construct(
                        #[ObjectExplode(objectName: 'object')]
                        public NestedObject $object,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'object[id]=1' . '&' . 'object[value]=foo',
            ],

            // Serializing null values.
            // Null value of non-sting types serialized to empty string.
            // Null value of string type is not serializable.
            'null int' => [
                'query' => new class (null) {
                    public function __construct(public ?int $value) {}
                },
                'allowReserved' => false,
                'expected' => 'value=',
            ],
            'null float' => [
                'query' => new class (null) {
                    public function __construct(public ?float $value) {}
                },
                'allowReserved' => false,
                'expected' => 'value=',
            ],
            'null array, Form, explode, no allow reserved' => [
                'query' => new class (null) {
                    public function __construct(
                        /** @var list<string>|null */
                        #[Form\ArrayExplode('value')]
                        public ?array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'value=',
            ],
            'null object, Form, no explode, no allow reserved' => [
                'query' => new class (null) {
                    public function __construct(
                        #[Form\ObjectNoExplode]
                        public ?NestedObject $object,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => 'object=',
            ],

            // Optional values
            'none string value' => [
                'query' => new readonly class (new None()) {
                    public function __construct(
                        public None|string $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => '',
            ],
            'none int value' => [
                'query' => new readonly class (new None()) {
                    public function __construct(
                        public None|int $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => '',
            ],
            'none float value' => [
                'query' => new readonly class (new None()) {
                    public function __construct(
                        public None|float $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => '',
            ],
            'none array value' => [
                'query' => new readonly class (new None()) {
                    public function __construct(
                        /** @var None|list<string> */
                        #[Form\ArrayNoExplode()]
                        public None|array $value,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => '',
            ],
            'none object value' => [
                'query' => new class (new None()) {
                    public function __construct(
                        #[Form\ObjectExplode]
                        public None|NestedObject $object,
                    ) {}
                },
                'allowReserved' => false,
                'expected' => '',
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testSerialize(object $query, bool $allowReserved, string $expected, bool $skip = false): void
    {
        if ($skip) {
            $this->markTestSkipped();
        }

        $actual = (new QuerySerializer())->serialize(
            query: $query,
            allowReserved: $allowReserved,
        );

        self::assertEquals($expected, $actual);
    }

    private static function stringQueryObject(string $value): object
    {
        return new class ($value) {
            public function __construct(public string $value) {}
        };
    }

    private static function intQueryObject(int $value): object
    {
        return new class ($value) {
            public function __construct(public int $value) {}
        };
    }

    private static function floatQueryObject(float $value): object
    {
        return new class ($value) {
            public function __construct(public float $value) {}
        };
    }
}
