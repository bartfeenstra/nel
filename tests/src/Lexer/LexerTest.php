<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Lexer;

use Bartfeenstra\Nel\Lexer\Lexer;
use Bartfeenstra\Nel\Lexer\Token;
use Bartfeenstra\Nel\Lexer\TokenType;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    public function testEmpty(): void
    {
        $sut = new Lexer('');
        $this->assertEquals([], \iterator_to_array($sut->tokenize()));
    }

    public function testSingleWhitespace(): void {
        $sut = new Lexer(' ');
        $this->assertEquals([new Token(TokenType::WHITESPACE, ' ', 0)], \iterator_to_array($sut->tokenize()));
    }

    public function testMultipleWhitespace(): void {
        $sut = new Lexer('   ');
        $this->assertEquals([new Token(TokenType::WHITESPACE, '   ', 0)], \iterator_to_array($sut->tokenize()));
    }

    public function testLiteralString(): void
    {
        $sut = new Lexer('"123"');
        $this->assertEquals([new Token(TokenType::LITERAL_STRING, '123', 0)], \iterator_to_array($sut->tokenize()));
    }

    public function testLiteralInt(): void {
        $sut = new Lexer('123');
        $this->assertEquals([new Token(TokenType::LITERAL_INTEGER, 123, 0)], \iterator_to_array($sut->tokenize()));
    }
}
