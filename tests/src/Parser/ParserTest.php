<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Parser;

use Bartfeenstra\Nel\Lexer\BooleanToken;
use Bartfeenstra\Nel\Lexer\IntegerToken;
use Bartfeenstra\Nel\Lexer\ListCloseToken;
use Bartfeenstra\Nel\Lexer\ListOpenToken;
use Bartfeenstra\Nel\Lexer\NamespaceToken;
use Bartfeenstra\Nel\Lexer\NameToken;
use Bartfeenstra\Nel\Lexer\NullToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\SeparatorToken;
use Bartfeenstra\Nel\Lexer\StringToken;
use Bartfeenstra\Nel\Lexer\WhitespaceToken;
use Bartfeenstra\Nel\Operator\AddOperator;
use Bartfeenstra\Nel\Operator\ContainsOperator;
use Bartfeenstra\Nel\Operator\InOperator;
use Bartfeenstra\Nel\Operator\MultiplyOperator;
use Bartfeenstra\Nel\Operator\NotOperator;
use Bartfeenstra\Nel\Parser\BinaryOperatorExpression;
use Bartfeenstra\Nel\Parser\BooleanExpression;
use Bartfeenstra\Nel\Parser\DataExpression;
use Bartfeenstra\Nel\Parser\FieldExpression;
use Bartfeenstra\Nel\Parser\IntegerExpression;
use Bartfeenstra\Nel\Parser\ListExpression;
use Bartfeenstra\Nel\Parser\NullExpression;
use Bartfeenstra\Nel\Parser\Parser;
use Bartfeenstra\Nel\Parser\StringExpression;
use Bartfeenstra\Nel\Parser\UnaryOperatorExpression;
use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\StructType;
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

    public function testParseData(): void
    {
        $dataType = new IntegerType();
        $parserDataType = new StructType('foo', [
            'foo' => $dataType,
        ]);
        $sut = new Parser([
            new NameToken(0, 'foo'),
        ], $parserDataType);
        $this->assertEquals(
            new DataExpression($dataType, 'foo'),
            $sut->parse(),
        );
    }

    public function testParseFields(): void
    {
        $bazType = new IntegerType();
        $barType = new StructType('bar', [
            'baz' => $bazType,
        ]);
        $fooType = new StructType('foo', [
            'bar' => $barType,
        ]);
        $dataType = new StructType('data', [
            'foo' => $fooType,
        ]);
        $sut = new Parser([
            new NameToken(0, 'foo'),
            new NamespaceToken(3),
            new NameToken(4, 'bar'),
            new NamespaceToken(7),
            new NameToken(8, 'baz'),
        ], $dataType);
        $this->assertEquals(
            new FieldExpression(
                new FieldExpression(
                    new DataExpression($fooType, 'foo'),
                    'bar',
                ),
                'baz'
            ),
            $sut->parse(),
        );
    }

    public function testParseEmptyList(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new ListCloseToken(1),
        ]);
        $this->assertEquals(
            new ListExpression([]),
            $sut->parse(),
        );
    }

    public function testParseEmptyListWithSeparators(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new SeparatorToken(1),
            new SeparatorToken(2),
            new SeparatorToken(3),
            new ListCloseToken(4),
        ]);
        $this->assertEquals(
            new ListExpression([]),
            $sut->parse(),
        );
    }

    public function testParseListWithSingleValue(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new IntegerToken(1, 123),
            new ListCloseToken(2),
        ]);
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
            ]),
            $sut->parse(),
        );
    }

    public function testParseListWithMultipleValues(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new IntegerToken(1, 123),
            new SeparatorToken(4),
            new IntegerToken(5, 456),
            new ListCloseToken(8),
        ]);
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
                new IntegerExpression(456),
            ]),
            $sut->parse(),
        );
    }

    public function testParseListWithMultipleValuesAndWhitespace(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new WhitespaceToken(1, ' '),
            new IntegerToken(2, 123),
            new WhitespaceToken(5, ' '),
            new SeparatorToken(6),
            new WhitespaceToken(7, ' '),
            new IntegerToken(8, 456),
            new WhitespaceToken(11, ' '),
            new ListCloseToken(12),
        ]);
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
                new IntegerExpression(456),
            ]),
            $sut->parse(),
        );
    }

    public function testParseListAndContainsOperator(): void
    {
        $sut = new Parser([
            new ListOpenToken(0),
            new IntegerToken(1, 123),
            new ListCloseToken(4),
            new OperatorToken(5, ContainsOperator::get()),
            new IntegerToken(1, 123),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                ContainsOperator::get(),
                new ListExpression([
                    new IntegerExpression(123),
                ]),
                new IntegerExpression(123),
            ),
            $sut->parse(),
        );
    }

    public function testParseListAndInOperator(): void
    {
        $sut = new Parser([
            new IntegerToken(1, 123),
            new OperatorToken(4, InOperator::get()),
            new ListOpenToken(5),
            new IntegerToken(6, 123),
            new ListCloseToken(9),
        ]);
        $this->assertEquals(
            new BinaryOperatorExpression(
                InOperator::get(),
                new IntegerExpression(123),
                new ListExpression([
                    new IntegerExpression(123),
                ]),
            ),
            $sut->parse(),
        );
    }
}
