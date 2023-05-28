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

final class Parser
{
    private int $cursor;

    private int $end;

    private bool $endOfFile;

    private ?Expression $expression;

    public function __construct(
        public readonly array $tokens,
    ) {
        $this->cursor = 0;
        $this->end = count($this->tokens);
        $this->endOfFile = false;
        $this->expression = null;
    }

    public function parse(): ?Expression
    {
        try {
            while ($this->cursor < $this->end) {
                $this->expression = $this->parseExpression();
            }
        } catch (EndOfFile) {
        }
        return $this->expression;
    }

    private function parseExpression(int $precedence = 0): Expression
    {
        if ($this->is(DoNotParseToken::class)) {
            $this->consume();
            return $this->parseExpression($precedence);
        }

        if ($this->is(ExpressionFactoryToken::class)) {
            return $this->consume()->expression();
        }

        // Operators.
        if ($this->is(OperatorToken::class)) {
            $operator = $this->consume()->operator;
            if ($operator instanceof UnaryOperator) {
                return new UnaryOperatorExpression(
                    $operator,
                    $this->parseExpression(),
                );
            } elseif ($operator instanceof BinaryOperator) {
                return new BinaryOperatorExpression(
                    $operator,
                    $this->expression,
                    $this->parseExpression(
                        Associativity::LEFT === $operator->associativity
                            ?
                            $operator->precedence + 1
                            :
                            $operator->precedence,
                    ),
                );
            }
        }

        throw new \LogicException('This must never happen.');
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
