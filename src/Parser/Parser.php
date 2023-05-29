<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\EndOfFile;
use Bartfeenstra\Nel\Lexer\DataDotToken;
use Bartfeenstra\Nel\Lexer\DataFieldToken;
use Bartfeenstra\Nel\Lexer\DoNotParseToken;
use Bartfeenstra\Nel\Lexer\ExpressionFactoryToken;
use Bartfeenstra\Nel\Lexer\ListCloseToken;
use Bartfeenstra\Nel\Lexer\ListOpenToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\SeparatorToken;
use Bartfeenstra\Nel\Lexer\Token;
use Bartfeenstra\Nel\Operator\Associativity;
use Bartfeenstra\Nel\Operator\BinaryOperator;
use Bartfeenstra\Nel\Operator\UnaryOperator;
use Bartfeenstra\Nel\ParseError;

final class Parser
{
    private int $cursor;

    private int $end;

    private bool $endOfFile;

    /**
     * @param list<\Bartfeenstra\Nel\Lexer\Token> $tokens
     */
    public function __construct(
        public readonly array $tokens,
    ) {
        $this->cursor = 0;
        $this->end = count($this->tokens);
        $this->endOfFile = false;
    }

    public function parse(): ?Expression
    {
        return $this->tokens ? $this->parseExpression() : null;
    }

    private function parseExpression(int $precedence = 0): ?Expression
    {
        if ($this->is(DoNotParseToken::class)) {
            $this->consume();
            /** @phpstan-ignore-next-line */
            while (!$this->endOfFile and $this->is(DoNotParseToken::class)) {
                $this->consume();
            }
            if ($this->endOfFile) {
                return null;
            }
        }

        $expression = null;

        if ($this->is(ExpressionFactoryToken::class)) {
            /** @var ExpressionFactoryToken $token */
            $token = $this->consume();
            $expression = $token->expression();
        } elseif ($this->is(OperatorToken::class)) {
            /** @var OperatorToken $token */
            $token = $this->current();
            $operator = $token->operator;
            if ($operator instanceof UnaryOperator) {
                $this->consume();
                $expression = new UnaryOperatorExpression(
                    $operator,
                    $this->expectExpression(),
                );
            }
        } elseif ($this->is(DataDotToken::class)) {
            $fields = [];
            /** @phpstan-ignore-next-line */
            while (!$this->endOfFile and $this->is(DataDotToken::class)) {
                /** @var DataDotToken $token */
                $this->consume();
                /** @phpstan-ignore-next-line */
                if (!$this->endOfFile and $this->is(DataFieldToken::class)) {
                    /** @var DataFieldToken $token */
                    $token = $this->consume();
                    $fields[] = $token->field;
                }
            }
            $expression = new DataExpression($fields);
        } elseif ($this->is(ListOpenToken::class)) {
            $this->consume();
            $values = [];
            while (!$this->endOfFile and !$this->is(ListCloseToken::class)) {
                if ($this->is(SeparatorToken::class)) {
                    $this->consume();
                    continue;
                }
                $valueExpression = $this->parseExpression();
                if ($valueExpression) {
                    $values[] = $valueExpression;
                }
            }
            if ($this->is(ListCloseToken::class)) {
                $this->consume();
            } else {
                throw new ParseError('List was not closed.');
            }
            $expression = new ListExpression($values);
        }

        // Finally, parse binary operators and see if they take the parsed expression as a left operand.
        while (!$this->endOfFile and $this->is(OperatorToken::class)) {
            /** @var OperatorToken $token */
            $token = $this->current();
            $operator = $token->operator;
            if ($operator instanceof BinaryOperator and $operator->precedence > $precedence) {
                if (!$expression) {
                    throw new ParseError(sprintf(
                        'Operator "%s" expects a left operand, but found nothing.',
                        $operator->token,
                    ));
                }
                $this->consume();
                $rightOperand = $this->expectExpression(
                    Associativity::LEFT === $operator->associativity
                    ?
                    $operator->precedence + 1
                    :
                    $operator->precedence,
                );
                $expression = new BinaryOperatorExpression(
                    $operator,
                    $expression,
                    $rightOperand,
                );
            } else {
                break;
            }
        }

        return $expression;
    }

    private function expectExpression(int $precedence = 0): Expression
    {
        $expression = $this->parseExpression($precedence);
        if ($expression) {
            return $expression;
        }
        throw new ParseError('Expected another expression, but found nothing.');
    }

    private function is(string $type): bool
    {
        return $this->current() instanceof $type;
    }

    private function current(): Token
    {
        if ($this->endOfFile) {
            throw new EndOfFile();
        }
        return $this->tokens[$this->cursor];
    }

    private function consume(): Token
    {
        $token = $this->current();
        $this->cursor++;
        if ($this->cursor === $this->end) {
            $this->endOfFile = true;
        }
        return $token;
    }
}
