<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Lexer;

use Bartfeenstra\Nel\Lexer\Lexer;
use Bartfeenstra\Nel\Lexer\Token;
use Bartfeenstra\Nel\Lexer\TokenType;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    public static function provideTokenize() : array {
        return [
            // An empty source.
            [[], ''],
            // A single whitespace.
            [[
                new Token(TokenType::WHITESPACE, ' ', 0),
            ], ' '],
            // Multiple whitespaces.
            [[
                new Token(TokenType::WHITESPACE, '   ', 0),
            ], '   '],
            // A string.
            [[
                new Token(TokenType::STRING, '123', 0),
            ], '"123"'],
            // An integer.
            [[
                new Token(TokenType::INTEGER, 123, 0),
            ], '123'],
            // Operators.
            [[
                new Token(TokenType::OPERATOR, 'startswith', 0),
            ], 'startswith'],
            [[
                new Token(TokenType::OPERATOR, 'endswith', 0),
            ], 'endswith'],
            [[
                new Token(TokenType::OPERATOR, 'contains', 0),
            ], 'contains'],
            [[
                new Token(TokenType::OPERATOR, 'in', 0),
            ], 'in'],
            [[
                new Token(TokenType::OPERATOR, 'and', 0),
            ], 'and'],
            [[
                new Token(TokenType::OPERATOR, 'or', 0),
            ], 'or'],
            [[
                new Token(TokenType::OPERATOR, '<=', 0),
            ], '<='],
            [[
                new Token(TokenType::OPERATOR, '<', 0),
            ], '<'],
            [[
                new Token(TokenType::OPERATOR, '=', 0),
            ], '='],
            [[
                new Token(TokenType::OPERATOR, '!=', 0),
            ], '!='],
            [[
                new Token(TokenType::OPERATOR, '>', 0),
            ], '>'],
            [[
                new Token(TokenType::OPERATOR, '>=', 0),
            ], '>='],
        ];
    }

    /**
     * @dataProvider provideTokenize
     */
    public function testTokenize(array $expectedTokens, string $source): void
    {
        $sut = new Lexer($source);
        $this->assertEquals($expectedTokens, \iterator_to_array($sut->tokenize()));
    }
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
        $this->assertEquals([new Token(TokenType::STRING, '123', 0)], \iterator_to_array($sut->tokenize()));
    }

    public function testLiteralInt(): void {
        $sut = new Lexer('123');
        $this->assertEquals([new Token(TokenType::INTEGER, 123, 0)], \iterator_to_array($sut->tokenize()));
    }

    public function testOperator(): void {
        $sut = new Lexer('<=');
        $this->assertEquals([new Token(TokenType::OPERATOR, '<=', 0)], \iterator_to_array($sut->tokenize()));
    }
}
