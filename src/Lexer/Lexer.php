<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\EndOfFile;
use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\SyntaxError;

final class Lexer
{
    private int $cursor;
    private int $end;
    private bool $endOfFile;

    public function __construct(
        public readonly string $source,
    ) {
        $this->cursor = 0;
        $this->end = \mb_strlen($source);
        $this->endOfFile = false;
    }

    /**
     * @return \Traversable<int, Token>
     */
    public function tokenize(): \Traversable
    {
        try {
            while ($this->cursor < $this->end) {
                // Whitespace.
                if ($this->isWhitespace()) {
                    $whitespaceStart = $this->cursor;
                    $whitespace = $this->consume();
                    // Combine consecutive whitespace into the same token.
                    try {
                        while ($this->isWhitespace()) {
                            $whitespace .= $this->consume();
                        }
                    } catch (EndOfFile) {
                    }
                    yield new WhitespaceToken(
                        $whitespaceStart,
                        $whitespace,
                    );
                    continue;
                }

                // Booleans.
                $boolean = $this->isOneOf(['true', 'false']);
                if ($boolean) {
                    yield new BooleanToken($this->cursor, 'true' === $boolean);
                    $this->consume(\strlen($boolean));
                    continue;
                }

                // null.
                $null = $this->is('null');
                if ($null) {
                    yield new NullToken($this->cursor);
                    $this->consume(4);
                    continue;
                }

                // Strings.
                $stringMatches = $this->isPregMatch(
                    '/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As',
                    true,
                );
                if ($stringMatches) {
                    [$stringSource, $stringValue] = $stringMatches;
                    yield new StringToken($this->cursor, $stringValue);
                    $this->consume(\mb_strlen($stringSource));
                    continue;
                }

                // Integers.
                $integerMatches = $this->isPregMatch('/^(\d+)/');
                if ($integerMatches) {
                    [$integerSource, $integerValue] = $integerMatches;
                    yield new IntegerToken($this->cursor, (int)$integerValue);
                    $this->consume(\strlen($integerSource));
                    continue;
                }

                // Lists.
                if ($this->is('[')) {
                    yield new ListOpenToken($this->cursor);
                    $this->consume();
                    continue;
                }
                if ($this->is(']')) {
                    yield new ListCloseToken($this->cursor);
                    $this->consume();
                    continue;
                }
                if ($this->is(',')) {
                    yield new SeparatorToken($this->cursor);
                    $this->consume();
                    continue;
                }

                // Data.
                if ($this->is('.')) {
                    yield new DataDotToken($this->cursor);
                    $this->consume();
                    $dataFieldMatches = $this->isPregMatch('/^([a-zA-Z0-9]+)/');
                    if ($dataFieldMatches) {
                        $dataField = $dataFieldMatches[0];
                        yield new DataFieldToken($this->cursor, $dataField);
                        $this->consume(\strlen($dataField));
                    }
                    continue;
                }

                // Operators.
                $operatorTokenValue = $this->isOneOf(array_map(
                    fn(Operator $operator) => $operator->token,
                    Operator::operators(),
                ));
                if ($operatorTokenValue) {
                    yield new OperatorToken($this->cursor, Operator::operator($operatorTokenValue));
                    $this->consume(\strlen($operatorTokenValue));
                    continue;
                }

                throw new SyntaxError(
                    $this->current(),
                    $this->cursor,
                    $this->source,
                );
            }
        } catch (EndOfFile) {
        }
    }

    /**
     * @param list<string> $sourceNeedles
     */
    private function isOneOf(array $sourceNeedles): ?string
    {
        $source = \mb_substr($this->source, $this->cursor);
        foreach ($sourceNeedles as $sourceNeedle) {
            if (str_starts_with($source, $sourceNeedle)) {
                return $sourceNeedle;
            }
        }
        return null;
    }

    private function is(string $sourceNeedle): ?string
    {
        return str_starts_with(\mb_substr($this->source, $this->cursor), $sourceNeedle) ? $sourceNeedle : null;
    }

    private function isWhitespace(): bool
    {
        return str_contains(' ', $this->current());
    }

    /**
     * @return list<string>|null
     */
    private function isPregMatch(string $pattern, bool $offset = false): ?array
    {
        $matches = [];
        $match = preg_match(
            $pattern,
            $offset ? $this->source : mb_substr($this->source, $this->cursor),
            $matches,
            0,
            $offset ? $this->cursor : 0,
        );
        if (false === $match) {
            throw new \RuntimeException(\preg_last_error_msg());
        }
        return 1 === $match ? $matches : null;
    }

    private function current(): string
    {
        if ($this->endOfFile) {
            throw new EndOfFile();
        }
        return $this->source[$this->cursor];
    }

    private function consume(int $count = 1): string
    {
        $value = '';
        while ($count) {
            $count--;
            $value .= $this->current();
            $this->cursor++;
            if ($this->cursor === $this->end) {
                $this->endOfFile = true;
            }
        }
        return $value;
    }
}
