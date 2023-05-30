<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Tests\Parser;

use Bartfeenstra\Nel\Lexer\Lexer;
use Bartfeenstra\Nel\Lexer\ListCloseToken;
use Bartfeenstra\Nel\Lexer\Token;
use Bartfeenstra\Nel\Operator\AddOperator;
use Bartfeenstra\Nel\Operator\ContainsOperator;
use Bartfeenstra\Nel\Operator\InOperator;
use Bartfeenstra\Nel\Operator\MultiplyOperator;
use Bartfeenstra\Nel\Operator\NotOperator;
use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\BooleanExpression;
use Bartfeenstra\Nel\Parser\DataExpression;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Parser\FieldExpression;
use Bartfeenstra\Nel\Parser\IntegerExpression;
use Bartfeenstra\Nel\Parser\ListExpression;
use Bartfeenstra\Nel\Parser\NullExpression;
use Bartfeenstra\Nel\Parser\OperatorExpression;
use Bartfeenstra\Nel\Parser\Parser;
use Bartfeenstra\Nel\Parser\StringExpression;
use Bartfeenstra\Nel\Type\IntegerType;
use Bartfeenstra\Nel\Type\StructType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-api
 */
#[CoversClass(Parser::class)]
final class ParserTest extends TestCase
{
    private function parse(
        string $source,
        ?StructType $dataType = null,
    ): ?Expression {
        $lexer = new Lexer($source);
        $parser = new Parser(iterator_to_array($lexer->tokenize()), $dataType);
        return $parser->parse();
    }
    public function testParseWithoutTokens(): void
    {
        $sut = new Parser([]);
        $this->assertNull($sut->parse());
    }

    public function testParseWhitespace(): void
    {
        $this->assertNull($this->parse(' '));
    }

    public function testParseString(): void
    {
        $this->assertEquals(
            new StringExpression('123'),
            $this->parse("'123'"),
        );
    }

    public function testParseInteger(): void
    {
        $this->assertEquals(
            new IntegerExpression(123),
            $this->parse('123'),
        );
    }

    public function testParseTrue(): void
    {
        $this->assertEquals(
            new BooleanExpression(true),
            $this->parse('true'),
        );
    }

    public function testParseFalse(): void
    {
        $this->assertEquals(
            new BooleanExpression(false),
            $this->parse('false'),
        );
    }

    public function testParseNull(): void
    {
        $this->assertEquals(
            new NullExpression(),
            $this->parse('null'),
        );
    }

    public function testParsePrefixOperator(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                NotOperator::get(),
                null,
                new BooleanExpression(true),
            ),
            $this->parse('not true'),
        );
    }

    public function testParseNestedPrefixOperator(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                NotOperator::get(),
                null,
                new OperatorExpression(
                    NotOperator::get(),
                    null,
                    new BooleanExpression(true),
                ),
            ),
            $this->parse('not not true'),
        );
    }

    public function testParseInfixOperator(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                AddOperator::get(),
                new IntegerExpression(123),
                new IntegerExpression(456),
            ),
            $this->parse('123 add 456'),
        );
    }

    public function testParseNestedInfixOperatorWithoutPrecedence(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                AddOperator::get(),
                new OperatorExpression(
                    AddOperator::get(),
                    new IntegerExpression(123),
                    new IntegerExpression(456),
                ),
                new IntegerExpression(789),
            ),
            $this->parse('123 add 456 add 789'),
        );
    }

    public function testParseNestedInfixOperatorWithImplicitPrecedence(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                AddOperator::get(),
                new OperatorExpression(
                    MultiplyOperator::get(),
                    new IntegerExpression(123),
                    new IntegerExpression(456),
                ),
                new IntegerExpression(789),
            ),
            $this->parse('123 mul 456 add 789'),
        );
    }

    public function testParseNestedInfixOperatorWithExplicitPrecedence(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                AddOperator::get(),
                new IntegerExpression(1),
                new OperatorExpression(
                    MultiplyOperator::get(),
                    new IntegerExpression(2),
                    new IntegerExpression(3),
                ),
            ),
            $this->parse('1 add 2 mul 3'),
        );
    }

    public function testParseData(): void
    {
        $dataType = new IntegerType();
        $parserDataType = new StructType('foo', [
            'foo' => $dataType,
        ]);
        $this->assertEquals(
            new DataExpression($dataType, 'foo'),
            $this->parse('foo', $parserDataType),
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
        $parserDataType = new StructType('data', [
            'foo' => $fooType,
        ]);
        $this->assertEquals(
            new FieldExpression(
                new FieldExpression(
                    new DataExpression($fooType, 'foo'),
                    'bar',
                ),
                'baz'
            ),
            $this->parse('foo.bar.baz', $parserDataType),
        );
    }

    public function testParseEmptyList(): void
    {
        $this->assertEquals(
            new ListExpression([]),
            $this->parse('[]'),
        );
    }

    public function testParseEmptyListWithSeparators(): void
    {
        $this->assertEquals(
            new ListExpression([]),
            $this->parse('[,,,]'),
        );
    }

    public function testParseListWithSingleValue(): void
    {
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
            ]),
            $this->parse('[123]'),
        );
    }

    public function testParseListWithMultipleValues(): void
    {
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
                new IntegerExpression(456),
            ]),
            $this->parse('[123,456]'),
        );
    }

    public function testParseListWithMultipleValuesAndWhitespace(): void
    {
        $this->assertEquals(
            new ListExpression([
                new IntegerExpression(123),
                new IntegerExpression(456),
            ]),
            $this->parse('[ 123 , 456 ]'),
        );
    }

    public function testParseListAndContainsOperator(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                ContainsOperator::get(),
                new ListExpression([
                    new IntegerExpression(123),
                ]),
                new IntegerExpression(123),
            ),
            $this->parse('[123] contains 123'),
        );
    }

    public function testParseListAndInOperator(): void
    {
        $this->assertEquals(
            new OperatorExpression(
                InOperator::get(),
                new IntegerExpression(123),
                new ListExpression([
                    new IntegerExpression(123),
                ]),
            ),
            $this->parse('123 in [123]'),
        );
    }

    public function testParseShouldThrowParseErrorOnUnexpectedToken(): void
    {
        $this->assertParseShouldThrowParseError(ListCloseToken::class, ']');
    }

    /**
     * @param class-string<Token> $expectedTokenClass
     */
    public function assertParseShouldThrowParseError(
        string $expectedTokenClass,
        string $source,
        ?StructType $dataType = null,
    ): void
    {
        try {
            $this->parse($source, $dataType);
        }
        catch (ParseError $e) {
            $this->assertInstanceOf($expectedTokenClass, $e->token);
            return;
        }
        // As odd as this looks, it means that while we check for the exception ourselves, we still throw the familiar
        // PHPUnit error.
        $this->expectException(ParseError::class);
    }
}
