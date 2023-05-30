<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\EndOfFile;
use Bartfeenstra\Nel\Lexer\DoNotParseToken;
use Bartfeenstra\Nel\Lexer\ExpressionFactoryToken;
use Bartfeenstra\Nel\Lexer\ListCloseToken;
use Bartfeenstra\Nel\Lexer\ListOpenToken;
use Bartfeenstra\Nel\Lexer\NamespaceToken;
use Bartfeenstra\Nel\Lexer\NameToken;
use Bartfeenstra\Nel\Lexer\OperatorToken;
use Bartfeenstra\Nel\Lexer\SeparatorToken;
use Bartfeenstra\Nel\Lexer\Token;
use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Type\StructType;
use Bartfeenstra\Nel\Type\Type;

final class Parser
{
    /**
     * @var list<Token> $tokens
     */
    private readonly array $tokens;

    private int $cursor;

    private int $end;

    private bool $endOfFile;

    /**
     * @param list<Token> $tokens
     */
    public function __construct(
        array $tokens,
        private readonly ?StructType $dataType = null,
    ) {
        $this->tokens = [...array_filter($tokens, fn($token) => !($token instanceof DoNotParseToken))];
        $this->cursor = 0;
        $this->end = count($this->tokens);
        $this->endOfFile = false;
    }

    public function parse(): ?Expression
    {
        if (!$this->tokens) {
            return null;
        }
        // @todo Do check for unexpected remaining tokens here
        $expression = $this->parseExpression();
        if (!$this->endOfFile) {
            $this->unexpectedTokenError();
        }
        return $expression;
    }

    private function parseExpression(int $precedence = 0): ?Expression
    {
        $expression = null;

        while (!$this->endOfFile) {
            $iterationCursorStart = $this->cursor;

            if ($this->is(ExpressionFactoryToken::class)) {
                /** @var ExpressionFactoryToken $token */
                $token = $this->consume();
                $expression = $token->expression();
            } elseif ($this->is(NameToken::class)) {
                /** @var NameToken $token */
                $token = $this->consume();
                $dataType = $this->dataType;
                if (!$dataType or !array_key_exists($token->name, $dataType->fields)) {
                    $this->error(sprintf('No data with name "%s" exists in this context.', $token->name));
                }
                /** @var StructType $dataType */
                $expression = new DataExpression($dataType->fieldType($token->name), $token->name);
            } elseif ($this->is(ListOpenToken::class)) {
                $this->consume();
                $values = [];
                $valueExpression = $this->parseExpression();
                if ($valueExpression) {
                    $values[] = $valueExpression;
                }
                do {
                    if ($this->is(SeparatorToken::class)) {
                        $this->consume();
                        continue;
                    }
                    if ($this->is(ListCloseToken::class)) {
                        $this->consume();
                        break;
                    }
                    $valueExpression = $this->parseExpression();
                    if ($valueExpression) {
                        $values[] = $valueExpression;
                        continue;
                    }
                    $this->unexpectedTokenError();
                } while (!$this->endOfFile);
                $expression = new ListExpression($values);
            }

            while (!$this->endOfFile and $this->is(NamespaceToken::class)) {
                $this->consume();
                if (!$expression) {
                    $this->error('Operator "." expects a left operand, but found nothing.');
                }
                $nameToken = $this->consume();
                if (!($nameToken instanceof NameToken)) {
                    $this->error('Operator "." must be followed by a field name');
                }
                /** @var Expression $expression */
                /** @var NameToken $nameToken */
                $expression = new FieldExpression($expression, $nameToken->name);
            }

            while (!$this->endOfFile and $this->is(OperatorToken::class)) {
                /** @var OperatorToken $token */
                $token = $this->current();
                $operator = $token->operator;
                $leftOperandExpression = null;
                $rightOperandExpression = null;
                if ($operator->isPostfix()) {
                    /** @var \Bartfeenstra\Nel\Operator\Operand $leftOperand */
                    $leftOperand = $operator->leftOperand;
                    if ($leftOperand->precedence < $precedence) {
                        break;
                    }
                    if (!$expression) {
                        $this->error(sprintf(
                            'Operator "%s" expects a left operand, but found nothing.',
                            $operator->token,
                        ));
                    }
                    $leftOperandExpression = $expression;
                }
                $this->consume();
                if ($operator->isPrefix()) {
                    /** @var \Bartfeenstra\Nel\Operator\Operand $rightOperand */
                    $rightOperand = $operator->rightOperand;
                    $rightOperandExpression = $this->expectExpression($rightOperand->precedence);
                }
                $expression = new OperatorExpression(
                    $operator,
                    $leftOperandExpression,
                    $rightOperandExpression,
                );
            }

            // Break out if this iteration has not parsed any tokens.
            if ($iterationCursorStart === $this->cursor) {
                return $expression;
            }
        }

        return $expression;
    }

    private function error(string $message): void
    {
        $token = $this->endOfFile ? null : $this->current();
        throw new ParseError($token, $message);
    }

    private function unexpectedTokenError(): void
    {
        $this->error(sprintf('Unexpected token %s.', $this->current()::class));
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

    private function expectExpression(int $precedence = 0, Type|string|null $type = null): Expression
    {
        $expression = $this->parseExpression($precedence);
        if ($expression) {
            if ($type and !($expression->type() instanceof $type)) {
                $this->error(sprintf(
                    'Expected a %s expression, but found %s instead.',
                    $type,
                    $expression->type(),
                ));
            }
            return $expression;
        }
        if ($type) {
            $this->error(sprintf(
                'Expected a %s expression, but found nothing.',
                $type,
            ));
        }
        $this->error('Expected another expression, but found nothing.');
    }
}
