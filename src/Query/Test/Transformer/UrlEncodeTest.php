<?php

namespace Transformer;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\ExplodeValues;
use YuiEzic\ValinorOpenapiSerializer\Query\Transformer\UrlEncode;

final class UrlEncodeTest extends TestCase
{
    public function testInvalidUpstreamTransformerInChain(): void
    {
        $this->expectException(RuntimeException::class);

        /**
         * @psalm-suppress InvalidArgument
         */
        (new MapperBuilder())
            ->registerTransformer(new UrlEncode())
            ->registerTransformer(fn(string $value) => ['value' => $value])
            ->normalizer(Format::array())
            ->normalize(new readonly class ('foo') {
                public function __construct(public string $value) {}
            });
    }
}
