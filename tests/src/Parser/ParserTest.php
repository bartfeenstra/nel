<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Parser;

use Bartfeenstra\Nel\Lexer\BooleanToken;
use Bartfeenstra\Nel\Lexer\IntegerToken;
use Bartfeenstra\Nel\Lexer\NullToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\StringToken;
use Bartfeenstra\Nel\Lexer\WhitespaceToken;
use Bartfeenstra\Nel\Operator\AddOperator;
use Bartfeenstra\Nel\Operator\IsOperator;
use Bartfeenstra\Nel\Operator\MultiplyOperator;
use Bartfeenstra\Nel\Operator\NotOperator;
use Bartfeenstra\Nel\Parser\BinaryOperatorExpression;
use Bartfeenstra\Nel\Parser\BooleanExpression;
use Bartfeenstra\Nel\Parser\IntegerExpression;
use Bartfeenstra\Nel\Parser\NullExpression;
use Bartfeenstra\Nel\Parser\Parser;
use Bartfeenstra\Nel\Parser\StringExpression;
use Bartfeenstra\Nel\Parser\UnaryOperatorExpression;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-api
 */
final class ParserTest extends TestCase
{
    public function testParseWithoutTokens(): void
    {
        $sut = new Parser([]);
        $this->assertNull($sut->parse());
    }

    public function testParseWhitespace(): void
    {
        $sut = new Parser([new WhitespaceToken(0, ' ')]);
        $this->assertNull($sut->parse());
    }

    public function testParseString(): void
    {
        $sut = new Parser([new StringToken(0, '123')]);
        $this->assertEquals(new StringExpression('123'), $sut->parse());
    }

    public function testParseInteger(): void
    {
        $sut = new Parser([new IntegerToken(0, 123)]);
        $this->assertEquals(new IntegerExpression(123), $sut->parse());
    }

    public function testParseTrue(): void
    {
        $sut = new Parser([new BooleanToken(0, true)]);
        $this->assertEquals(new BooleanExpression(true), $sut->parse());
    }

    public function testParseFalse(): void
    {
        $sut = new Parser([new BooleanToken(0, false)]);
        $this->assertEquals(new BooleanExpression(false), $sut->parse());
    }

    public function testParseNull(): void
    {
        $sut = new Parser([new NullToken(0)]);
        $this->assertEquals(new NullExpression(), $sut->parse());
    }

    public function testParseUnaryOperator(): void
    {
        $sut = new Parser([
            new OperatorToken(0, NotOperator::get()),
            new BooleanToken(0, true),
        ]);
        $this->assertEquals(
            new UnaryOperatorExpression(
                NotOperator::get(),
                new BooleanExpression(true),
            ),
            $sut->parse(),
        );
    }

    public function testParseNestedUnaryOperator(): void
    {
        $sut = new Parser([
            new OperatorToken(0, NotOperator::get()),
            new OperatorToken(0, NotOperator::get()),
            new BooleanToken(0, true),
        ]);
        $this->assertEquals(
            new UnaryOperatorExpression(
                NotOperator::get(),
                new UnaryOperatorExpression(
                    NotOperator::get(),
                    new BooleanExpression(true),
                ),
            ),
            $sut->parse(),
        );
    }

    public function testParseBinaryOperator(): void
    {
        $sut = new Parser([
            new IntegerToken(0, 123),
            new OperatorToken(0, AddOperator::get()),
            new IntegerToken(0, 456),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                AddOperator::get(),
                new IntegerExpression(123),
                new IntegerExpression(456),
            ),
            $sut->parse(),
        );
    }

    public function testParseNestedBinaryOperatorWithoutPrecedence(): void
    {
        $sut = new Parser([
            new IntegerToken(0, 123),
            new OperatorToken(0, MultiplyOperator::get()),
            new IntegerToken(0, 456),
            new OperatorToken(0, AddOperator::get()),
            new IntegerToken(0, 789),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                AddOperator::get(),
                new BinaryOperatorExpression(
                    MultiplyOperator::get(),
                    new IntegerExpression(123),
                    new IntegerExpression(456),
                ),
                new IntegerExpression(789),
            ),
            $sut->parse(),
        );
    }

    public function testParseNestedBinaryOperatorWithPrecedence(): void
    {
        $sut = new Parser([
            new IntegerToken(0, 1),
            new OperatorToken(0, AddOperator::get()),
            new IntegerToken(0, 2),
            new OperatorToken(0, MultiplyOperator::get()),
            new IntegerToken(0, 3),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                AddOperator::get(),
                new IntegerExpression(1),
                new BinaryOperatorExpression(
                    MultiplyOperator::get(),
                    new IntegerExpression(2),
                    new IntegerExpression(3),
                ),
            ),
            $sut->parse(),
        );
    }
}
