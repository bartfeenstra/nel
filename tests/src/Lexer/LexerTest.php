<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Lexer;

use Bartfeenstra\Nel\Lexer\BooleanToken;
use Bartfeenstra\Nel\Lexer\IntegerToken;
use Bartfeenstra\Nel\Lexer\Lexer;
use Bartfeenstra\Nel\Lexer\ListCloseToken;
use Bartfeenstra\Nel\Lexer\ListOpenToken;
use Bartfeenstra\Nel\Lexer\NamespaceToken;
use Bartfeenstra\Nel\Lexer\NameToken;
use Bartfeenstra\Nel\Lexer\NullToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\SeparatorToken;
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
use Bartfeenstra\Nel\SyntaxError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-api
 */
#[CoversClass(Lexer::class)]
final class LexerTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: list<\Bartfeenstra\Nel\Lexer\Token>, 1: string}>
     */
    public static function provideTokenize(): array
    {
        return [
            // An empty source.
            [[], ''],
            // A single whitespace.
            [[
                new WhitespaceToken(' ', 0, 0, ' '),
            ], ' '],
            // Multiple whitespaces.
            [[
                new WhitespaceToken('   ', 0, 0, '   '),
            ], '   '],
            // Booleans.
            [[
                new BooleanToken('true', 0, 0, true),
            ], 'true'],
            [[
                new BooleanToken('false', 0, 0, false),
            ], 'false'],
            // Null.
            [[
                new NullToken('null', 0, 0),
            ], 'null'],
            // A string.
            [[
                new StringToken('"123"', 0, 0, '123'),
            ], '"123"'],
            // An integer.
            [[
                new IntegerToken('123', 0, 0, 123),
            ], '123'],
            // Lists.
            [[
                new ListOpenToken('[]', 0, 0),
                new ListCloseToken('[]', 0, 1),
            ], '[]'],
            [[
                new ListOpenToken('[123]', 0, 0),
                new IntegerToken('[123]', 0, 1, 123),
                new ListCloseToken('[123]', 0, 4),
            ], '[123]'],
            [[
                new ListOpenToken('[123, 456]', 0, 0),
                new IntegerToken('[123, 456]', 0, 1, 123),
                new SeparatorToken('[123, 456]', 0, 4),
                new WhitespaceToken('[123, 456]', 0, 5, ' '),
                new IntegerToken('[123, 456]', 0, 6, 456),
                new ListCloseToken('[123, 456]', 0, 9),
            ], '[123, 456]'],
            [[
                new ListOpenToken('[,,,]', 0, 0),
                new SeparatorToken('[,,,]', 0, 1),
                new SeparatorToken('[,,,]', 0, 2),
                new SeparatorToken('[,,,]', 0, 3),
                new ListCloseToken('[,,,]', 0, 4),
            ], '[,,,]'],
            // Data.
            [[
                new NameToken('foo', 0, 0, 'foo'),
            ], 'foo'],
            // Fields.
            [[
                new NameToken('foo.bar', 0, 0, 'foo'),
                new NamespaceToken('foo.bar', 0, 3),
                new NameToken('foo.bar', 0, 4, 'bar'),
            ], 'foo.bar'],
            [[
                new NameToken('foo.bar.baz', 0, 0, 'foo'),
                new NamespaceToken('foo.bar.baz', 0, 3),
                new NameToken('foo.bar.baz', 0, 4, 'bar'),
                new NamespaceToken('foo.bar.baz', 0, 7),
                new NameToken('foo.bar.baz', 0, 8, 'baz'),
            ], 'foo.bar.baz'],
            // Operators.
            [[
                new OperatorToken(StartsWithOperator::get()->token, 0, 0, StartsWithOperator::get()),
            ], StartsWithOperator::get()->token],
            [[
                new OperatorToken(EndsWithOperator::get()->token, 0, 0, EndsWithOperator::get()),
            ], EndsWithOperator::get()->token],
            [[
                new OperatorToken(ContainsOperator::get()->token, 0, 0, ContainsOperator::get()),
            ], ContainsOperator::get()->token],
            [[
                new OperatorToken(InOperator::get()->token, 0, 0, InOperator::get()),
            ], InOperator::get()->token],
            [[
                new OperatorToken(AndOperator::get()->token, 0, 0, AndOperator::get()),
            ], AndOperator::get()->token],
            [[
                new OperatorToken(OrOperator::get()->token, 0, 0, OrOperator::get()),
            ], OrOperator::get()->token],
            [[
                new OperatorToken(IsOperator::get()->token, 0, 0, IsOperator::get()),
            ], IsOperator::get()->token],
            [[
                new OperatorToken(IsLessThanOrEqualsOperator::get()->token, 0, 0, IsLessThanOrEqualsOperator::get()),
            ], IsLessThanOrEqualsOperator::get()->token],
            [[
                new OperatorToken(IsLessThanOperator::get()->token, 0, 0, IsLessThanOperator::get()),
            ], IsLessThanOperator::get()->token],
            [[
                new OperatorToken(IsGreaterThanOrEqualsOperator::get()->token, 0, 0, IsGreaterThanOrEqualsOperator::get()),
            ], IsGreaterThanOrEqualsOperator::get()->token],
            [[
                new OperatorToken(IsGreaterThanOperator::get()->token, 0, 0, IsGreaterThanOperator::get()),
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

    /**
     * @return non-empty-list<array{0: int, 1: int, 2: string}>
     */
    public static function provideTokenizeShouldThrowSyntaxError(): array
    {
        return [
            // A character that is not part of the syntax.
            [0, 0, '_'],
            [0, 0, '_'],
        ];
    }

    /**
     * @dataProvider provideTokenizeShouldThrowSyntaxError
     */
    public function testTokenizeShouldThrowSyntaxError(int $expectedLine, int $expectedColumn, string $source): void
    {
        $sut = new Lexer($source);
        try {
            iterator_to_array($sut->tokenize());
        }
        catch (SyntaxError $e) {
            $this->assertSame($expectedLine, $e->sourceLine);
            $this->assertSame($expectedColumn, $e->sourceColumn);
            $this->assertSame($source, $e->source);
            return;
        }
        // As odd as this looks, it means that while we check for the exception ourselves, we still throw the familiar
        // PHPUnit error.
        $this->expectException(SyntaxError::class);
    }
}
