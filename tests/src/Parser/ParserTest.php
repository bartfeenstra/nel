<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Parser;

use Bartfeenstra\Nel\Lexer\BooleanToken;
use Bartfeenstra\Nel\Lexer\IntegerToken;
use Bartfeenstra\Nel\Lexer\NullToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\StringToken;
use Bartfeenstra\Nel\Lexer\WhitespaceToken;
use Bartfeenstra\Nel\Operator\IsOperator;
use Bartfeenstra\Nel\Operator\NotOperator;
use Bartfeenstra\Nel\Parser\BinaryOperatorExpression;
use Bartfeenstra\Nel\Parser\BooleanExpression;
use Bartfeenstra\Nel\Parser\IntegerExpression;
use Bartfeenstra\Nel\Parser\NullExpression;
use Bartfeenstra\Nel\Parser\Parser;
use Bartfeenstra\Nel\Parser\StringExpression;
use Bartfeenstra\Nel\Parser\UnaryOperatorExpression;
use PHPUnit\Framework\TestCase;

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

    public function testParseBinaryOperator(): void
    {
        $sut = new Parser([
            new IntegerToken(0, 123),
            new OperatorToken(0, IsOperator::get()),
            new IntegerToken(0, 456),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                IsOperator::get(),
                new IntegerExpression(123),
                new IntegerExpression(456),
            ),
            $sut->parse(),
        );
    }
}
