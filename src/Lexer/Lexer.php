<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Lexer;

use Bartfeenstra\Nel\EndOfFile;
use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\SyntaxError;
use RuntimeException;
use Traversable;

use function mb_strlen;
use function mb_substr;
use function preg_last_error_msg;
use function strlen;

final class Lexer
{
    private int $cursor;
    private int $line;
    private int $column;
    private int $end;
    private bool $endOfFile;

    public function __construct(
        public readonly string $source,
    ) {
        $this->cursor = 0;
        $this->line = 0;
        $this->column = 0;
        $this->end = mb_strlen($source);
        $this->endOfFile = false;
    }

    /**
     * @return Traversable<int, Token>
     */
    public function tokenize(): Traversable
    {
        try {
            while ($this->cursor < $this->end) {
                // Whitespace.
                if ($this->isWhitespace()) {
                    $line = $this->line;
                    $column = $this->column;
                    $whitespace = $this->consume();
                    // Combine consecutive whitespace into the same token.
                    /** @phpstan-ignore-next-line */
                    while (!$this->endOfFile and $this->isWhitespace()) {
                        $whitespace .= $this->consume();
                    }
                    yield new WhitespaceToken(
                        $this->source,
                        $line,
                        $column,
                        $whitespace,
                    );
                    continue;
                }

                // Booleans.
                $boolean = $this->isOneOf(['true', 'false']);
                if ($boolean) {
                    yield new BooleanToken(
                        $this->source,
                        $this->line,
                        $this->column,
                        'true' === $boolean,
                    );
                    $this->consume(strlen($boolean));
                    continue;
                }

                // null.
                $null = $this->is('null');
                if ($null) {
                    yield new NullToken(
                        $this->source,
                        $this->line,
                        $this->column,
                    );
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
                    yield new StringToken(
                        $this->source,
                        $this->line,
                        $this->column,
                        $stringValue,
                    );
                    $this->consume(mb_strlen($stringSource));
                    continue;
                }

                // Integers.
                $integerMatches = $this->isPregMatch('/^(\d+)/');
                if ($integerMatches) {
                    [$integerSource, $integerValue] = $integerMatches;
                    yield new IntegerToken(
                        $this->source,
                        $this->line,
                        $this->column,
                        (int) $integerValue,
                    );
                    $this->consume(strlen($integerSource));
                    continue;
                }

                // Lists.
                if ($this->is('[')) {
                    yield new ListOpenToken(
                        $this->source,
                        $this->line,
                        $this->column,
                    );
                    $this->consume();
                    continue;
                }
                if ($this->is(']')) {
                    yield new ListCloseToken(
                        $this->source,
                        $this->line,
                        $this->column,
                    );
                    $this->consume();
                    continue;
                }
                if ($this->is(',')) {
                    yield new SeparatorToken(
                        $this->source,
                        $this->line,
                        $this->column,
                    );
                    $this->consume();
                    continue;
                }

                // Operators.
                $operatorTokenValue = $this->isOneOf(array_map(
                    fn(Operator $operator) => $operator->token,
                    Operator::operators(),
                ));
                if ($operatorTokenValue) {
                    yield new OperatorToken(
                        $this->source,
                        $this->line,
                        $this->column,
                        Operator::operator($operatorTokenValue),
                    );
                    $this->consume(strlen($operatorTokenValue));
                    continue;
                }

                // Data.
                $dataMatches = $this->isPregMatch('/^([a-zA-Z0-9]+)/');
                if ($dataMatches) {
                    [$dataSource, $dataValue] = $dataMatches;
                    yield new NameToken(
                        $this->source,
                        $this->line,
                        $this->column,
                        $dataValue,
                    );
                    $this->consume(strlen($dataSource));
                    continue;
                }

                // Fields.
                if ($this->is('.')) {
                    yield new NamespaceToken(
                        $this->source,
                        $this->line,
                        $this->column,
                    );
                    $this->consume();
                    continue;
                }

                $this->error();
            }
        } catch (EndOfFile) {
        }
    }

    private function error(): void
    {
        throw new SyntaxError(
            $this->source,
            $this->line,
            $this->column,
            $this->current(),
        );
    }

    private function isWhitespace(): bool
    {
        return str_contains(implode('', [
            ' ',    // Space.
            '	',  // Tab.
            "\n",   // Newline.
        ]), $this->current());
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
            $currentValue = $this->current();
            $value .= $currentValue;
            $count--;
            $this->cursor++;
            $this->column++;
            if ($this->cursor === $this->end) {
                $this->endOfFile = true;
            }
            if ("\n" === $currentValue) {
                $this->line++;
                $this->column = 0;
            }
        }
        return $value;
    }

    /**
     * @param list<string> $sourceNeedles
     */
    private function isOneOf(array $sourceNeedles): ?string
    {
        $source = mb_substr($this->source, $this->cursor);
        foreach ($sourceNeedles as $sourceNeedle) {
            if (str_starts_with($source, $sourceNeedle)) {
                return $sourceNeedle;
            }
        }
        return null;
    }

    private function is(string $sourceNeedle): ?string
    {
        return str_starts_with(mb_substr($this->source, $this->cursor), $sourceNeedle) ? $sourceNeedle : null;
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
            throw new RuntimeException(preg_last_error_msg());
        }
        return 1 === $match ? $matches : null;
    }
}
