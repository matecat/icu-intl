<?php
/**
 * Created by PhpStorm.
 * @author Domenico Lupinetti (hashashiyyin) domenico@translated.net / ostico@gmail.com
 * Date: 04/02/26
 * Time: 17:54
 *
 */

namespace Matecat\ICU\Tests;

use Matecat\ICU\MessagePattern;
use Matecat\ICU\MessagePatternAnalyzer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MessagePatternAnalyzerTest extends TestCase
{

    #[Test]
    public function testContainsComplexSyntax(): void
    {
        $complexPattern = new MessagePattern();
        $complexPattern->parse('You have {count, plural, one{# file} other{# files}}.');
        $complexAnalyzer = new MessagePatternAnalyzer($complexPattern);
        self::assertTrue($complexAnalyzer->containsComplexSyntax());

        $simplePattern = new MessagePattern();
        $simplePattern->parse('Hello {name}.');
        $simpleAnalyzer = new MessagePatternAnalyzer($simplePattern);
        self::assertFalse($simpleAnalyzer->containsComplexSyntax());
    }

}