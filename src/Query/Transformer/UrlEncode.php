<?php

namespace YuiEzic\ValinorOpenapiSerializer\Query\Transformer;

use RuntimeException;

/**
 * Encodes values for url by RFC 3986
 */
readonly class UrlEncode
{
    private const string RESERVED = "/?#[]@!$&'()*+,;=";

    public function __construct(
        /**
         * If true preserves reserved characters from encoding
         */
        private bool $allowReserved = false,
    ) {}

    public function __invoke(string $value, callable $next): string
    {
        $result = $next();
        if (!is_string($result)) {
            throw new RuntimeException('Url encode expects a string from upstream transformer.');
        }
        return $this->encode($next());
    }

    private function encode(string $value): string
    {
        $encoded = '';

        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            if ($this->isShouldBeEncoded($value[$i])) {
                $encoded .= rawurlencode($value[$i]);
            } else {
                $encoded .= $value[$i];
            }
        }

        return $encoded;
    }

    private function isShouldBeEncoded(string $char): bool
    {
        if (str_contains(self::RESERVED, $char)) {
            return !$this->allowReserved;
        }
        return true;
    }
}
