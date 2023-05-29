<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Lexer;

use Bartfeenstra\Nel\Lexer\BooleanToken;
use Bartfeenstra\Nel\Lexer\IntegerToken;
use Bartfeenstra\Nel\Lexer\Lexer;
use Bartfeenstra\Nel\Lexer\NullToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\StringToken;
use Bartfeenstra\Nel\Lexer\WhitespaceToken;
use Bartfeenstra\Nel\Operator\AndOperator;
use Bartfeenstra\Nel\Operator\ContainsOperator;
use Bartfeenstra\Nel\Operator\EndsWithOperator;
use Bartfeenstra\Nel\Operator\InOperator;
use Bartfeenstra\Nel\Operator\IsGreaterThanOperator;
use Bartfeenstra\Nel\Operator\IsGreaterThanOrEqualsOperator;
use Bartfeenstra\Nel\Operator\IsLessThanOperator;
use Bartfeenstra\Nel\Operator\IsLessThanOrEqualsOperator;
use Bartfeenstra\Nel\Operator\IsOperator;
use Bartfeenstra\Nel\Operator\OrOperator;
use Bartfeenstra\Nel\Operator\StartsWithOperator;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-api
 */
final class LexerTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: list<\Bartfeenstra\Nel\Lexer\Token>, 1: string}>
     */
    public static function provideTokenize() : array {
        return [
            // An empty source.
            [[], ''],
            // A single whitespace.
            [[
                new WhitespaceToken(0, ' '),
            ], ' '],
            // Multiple whitespaces.
            [[
                new WhitespaceToken(0, '   '),
            ], '   '],
            // Booleans.
            [[
                new BooleanToken(0, true),
            ], 'true'],
            [[
                new BooleanToken(0, false),
            ], 'false'],
            // Null.
            [[
                new NullToken(0),
            ], 'null'],
            // A string.
            [[
                new StringToken(0, '123'),
            ], '"123"'],
            // An integer.
            [[
                new IntegerToken(0, 123),
            ], '123'],
            // Operators.
            [[
                new OperatorToken(0, StartsWithOperator::get()),
            ], StartsWithOperator::get()->token],
            [[
                new OperatorToken(0, EndsWithOperator::get()),
            ], EndsWithOperator::get()->token],
            [[
                new OperatorToken(0, ContainsOperator::get()),
            ], ContainsOperator::get()->token],
            [[
                new OperatorToken(0, InOperator::get()),
            ], InOperator::get()->token],
            [[
                new OperatorToken(0, AndOperator::get()),
            ], AndOperator::get()->token],
            [[
                new OperatorToken(0, OrOperator::get()),
            ], OrOperator::get()->token],
            [[
                new OperatorToken(0, IsOperator::get()),
            ], IsOperator::get()->token],
            [[
                new OperatorToken(0, IsLessThanOrEqualsOperator::get()),
            ], IsLessThanOrEqualsOperator::get()->token],
            [[
                new OperatorToken(0, IsLessThanOperator::get()),
            ], IsLessThanOperator::get()->token],
            [[
                new OperatorToken(0, IsGreaterThanOrEqualsOperator::get()),
            ], IsGreaterThanOrEqualsOperator::get()->token],
            [[
                new OperatorToken(0, IsGreaterThanOperator::get()),
            ], IsGreaterThanOperator::get()->token],
        ];
    }

    /**
     * @param array<int, \Bartfeenstra\Nel\Lexer\Token> $expectedTokens
     * @dataProvider provideTokenize
     */
    public function testTokenize(array $expectedTokens, string $source): void
    {
        $sut = new Lexer($source);
        $this->assertEquals($expectedTokens, \iterator_to_array($sut->tokenize()));
    }
}
