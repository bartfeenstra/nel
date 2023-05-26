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

                // String literals.
                $stringLiteralMatches = [];
                if (
                    $this->isPregMatch(
                        '/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As',
                        $stringLiteralMatches,
                    )
                ) {
                    [$integerLiteralSource, $integerLiteralValue] = $stringLiteralMatches;
                    yield new Token(TokenType::LITERAL_STRING, $integerLiteralValue, $this->cursor);
                    $this->consume(\mb_strlen($integerLiteralSource));
                    continue;
                }

                // Integer literals.
                $integerLiteralMatches = [];
                if ($this->isPregMatch('/(.+)/', $integerLiteralMatches)) {
                    [$integerLiteralSource, $integerLiteralValue] = $integerLiteralMatches;
                    yield new Token(TokenType::LITERAL_INTEGER, $integerLiteralValue, $this->cursor);
                    $this->consume(\mb_strlen($integerLiteralSource));
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

    private function isWhitespace(): bool
    {
        return str_contains(' ', $this->current());
    }

    private function isPregMatch(string $pattern, array &$matches): bool
    {
        $match = preg_match($pattern, $this->remainder(), $matches);
        if (false === $match) {
            throw new \RuntimeException(\preg_last_error_msg());
        }
        return (bool) $match;
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
