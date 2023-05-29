<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\EndOfFile;
use Bartfeenstra\Nel\Lexer\DoNotParseToken;
use Bartfeenstra\Nel\Lexer\ExpressionFactoryToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
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
            try {
                while ($this->is(DoNotParseToken::class)) {
                    $this->consume();
                }
            } catch (EndOfFile) {
                return null;
            }
        }

        if ($this->is(ExpressionFactoryToken::class)) {
            $expression = $this->consume()->expression();
        } elseif ($this->is(OperatorToken::class) and $this->current()->operator instanceof UnaryOperator) {
            $operator = $this->consume()->operator;
            $expression = new UnaryOperatorExpression(
                $operator,
                $this->expectExpression(),
            );
        }

        // Finally, parse binary operators and see if they take the parsed expression as a left operand.
        while (!$this->endOfFile and $this->is(OperatorToken::class)) {
            $operator = $this->current()->operator;
            if ($operator instanceof BinaryOperator and $operator->precedence > $precedence) {
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
