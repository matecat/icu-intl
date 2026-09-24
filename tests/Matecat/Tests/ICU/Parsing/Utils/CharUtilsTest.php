<?php

namespace Matecat\Tests\ICU\Parsing\Utils;

use Matecat\ICU\Parsing\Utils\CharUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CharUtilsTest extends TestCase
{
    /**
     * @return array<string, array{0: ?string, 1: bool}>
     */
    public static function argTypeCharProvider(): array
    {
        return [
            'lowercase letter' => ['a', true],
            'uppercase letter' => ['Z', true],
            'digit zero' => ['0', false],   // empty('0') is true: must not rely on empty()
            'digit' => ['7', false],
            'empty string' => ['', false],
            'null' => [null, false],
            'whitespace' => [' ', false],
            'comma' => [',', false],
        ];
    }

    #[Test]
    #[DataProvider('argTypeCharProvider')]
    public function testIsArgTypeChar(?string $c, bool $expected): void
    {
        self::assertSame($expected, CharUtils::isArgTypeChar($c));
    }
}
