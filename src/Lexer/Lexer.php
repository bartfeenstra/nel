<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

final class Lexer
{
    private int $cursor;
    private int $end;
    private bool $eof;

    public function __construct(
        public readonly string $source,
    ) {
        $this->cursor = 0;
        $this->end = \mb_strlen($source);
        $this->eof = false;
    }

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
                    yield new Token(
                        TokenType::WHITESPACE,
                        $whitespace,
                        $whitespaceStart,
                    );
                    continue;
                }

                // Strings.
                $stringMatches = $this->isPregMatch(
                    '/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As',
                );
                if ($stringMatches) {
                    [$stringSource, $stringValue] = $stringMatches;
                    yield new Token(TokenType::STRING, $stringValue, $this->cursor);
                    $this->consume(\mb_strlen($stringSource));
                    continue;
                }

                // Integers.
                $integerMatches = $this->isPregMatch('/(\d+)/');
                if ($integerMatches) {
                    [$integerSource, $integerValue] = $integerMatches;
                    yield new Token(TokenType::INTEGER, $integerValue, $this->cursor);
                    $this->consume(\strlen($integerSource));
                    continue;
                }

                // Operators.
                $operator = $this->isOneOf([
                    'startswith',
                    'endswith',
                    'contains',
                    'in',
                    'and',
                    'or',
                    '<=',
                    '>=',
                    '!=',
                    '<',
                    '>',
                    '=',
                ]);
                if ($operator) {
                    yield new Token(TokenType::OPERATOR, $operator, $this->cursor);
                    $this->consume(\strlen($operator));
                    continue;
                }

                throw new \InvalidArgumentException(sprintf(
                    'Unknown token "%s" at position %d of "%s".',
                    $this->current(),
                    $this->cursor,
                    $this->source,
                ));
            }
        } catch (EndOfFile) {
        }
    }

    private function isOneOf(array $sourceNeedles): ?string
    {
        $source = \mb_substr($this->source, $this->cursor);
        foreach ($sourceNeedles as $sourceNeedle) {
            if (str_starts_with($source, $sourceNeedle)) {
                return $sourceNeedle;
            }
        }
    }

    private function isWhitespace(): bool
    {
        return str_contains(' ', $this->current());
    }

    private function isPregMatch(string $pattern): ?array
    {
        $matches = [];
        $match = preg_match($pattern, $this->source, $matches, 0, $this->cursor);
        if (false === $match) {
            throw new \RuntimeException(\preg_last_error_msg());
        }
        return 1 === $match ? $matches : null;
    }

    private function current(): string
    {
        if ($this->eof) {
            throw new EndOfFile();
        }
        return $this->source[$this->cursor];
    }

    private function remainder(): string
    {
        return \mb_substr($this->source, $this->cursor);
    }

    private function consume(int $count = 1): string
    {
        $value = '';
        while ($count) {
            $count--;
            $value .= $this->current();
            $this->cursor++;
            if ($this->cursor === $this->end) {
                $this->eof = true;
            }
        }
        return $value;
    }
}
