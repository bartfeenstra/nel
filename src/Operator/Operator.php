<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Parser\Expression;
use Bartfeenstra\Nel\Type\Type;
use InvalidArgumentException;

abstract class Operator
{
    /**
     * @var Array<self>
     */
    private static array $instances = [];

    protected function __construct(
        public readonly string $token,
        public readonly ?Operand $leftOperand,
        public readonly ?Operand $rightOperand,
    ) {
    }

    public static function operator(string $token): Operator
    {
        foreach (static::operators() as $operator) {
            if ($token === $operator->token) {
                return $operator;
            }
        }
        throw new InvalidArgumentException(sprintf('Unknown operator with token "%s".', $token));
    }

    /**
     * @return non-empty-list<Operator>
     */
    public static function operators(): array
    {
        return [
            StartsWithOperator::get(),
            EndsWithOperator::get(),
            ContainsOperator::get(),
            InOperator::get(),
            AndOperator::get(),
            OrOperator::get(),
            IsLessThanOrEqualsOperator::get(),
            IsGreaterThanOrEqualsOperator::get(),
            IsLessThanOperator::get(),
            IsGreaterThanOperator::get(),
            IsNotOperator::get(),
            IsOperator::get(),
            NotOperator::get(),
            AddOperator::get(),
            SubtractOperator::get(),
            MultiplyOperator::get(),
        ];
    }

    public static function get(): static
    {
        if (!in_array(static::class, self::$instances)) {
            /**
             * @psalm-suppress TooFewArguments
             * @psalm-suppress UnsafeInstantiation
             * @phpstan-ignore-next-line
             */
            self::$instances[static::class] = new static();
        }
        /** @phpstan-ignore-next-line */
        return self::$instances[static::class];
    }

    abstract public function type(): Type;

    public function validateLeftOperand(Expression $operand): void
    {
        $this->assertPostfix();
        /** @var Operand $leftOperand */
        $leftOperand = $this->leftOperand;
        if ($operand->type() != ($leftOperand->type)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its left operand to be %s, but instead it evaluates to %s.',
                $this->token,
                $leftOperand->type,
                $operand->type(),
            ));
        }
    }

    public function validateRightOperand(Expression $operand): void
    {
        $this->assertPrefix();
        /** @var Operand $rightOperand */
        $rightOperand = $this->rightOperand;
        if ($operand->type() != ($rightOperand->type)) {
            throw new ParseError(null, sprintf(
                'Operator "%s" expects its right operand to be %s, but instead it evaluates to %s.',
                $this->token,
                $rightOperand->type,
                $operand->type(),
            ));
        }
    }

    public function validateOperands(Expression $leftOperand, Expression $rightOperand): void
    {
        $this->validateLeftOperand($leftOperand);
        $this->validateRightOperand($rightOperand);
    }

    public function isPrefix(): bool
    {
        return null !== $this->rightOperand;
    }

    private function assertPrefix(): void
    {
        if (!$this->isPrefix()) {
            throw new \RuntimeException(sprintf(
                'Operator "%s" is not a prefix operator and does not take a right operand.',
                $this->token,
            ));
        }
    }

    public function isPostfix(): bool
    {
        return null !== $this->leftOperand;
    }

    private function assertPostfix(): void
    {
        if (!$this->isPostfix()) {
            throw new \RuntimeException(sprintf(
                'Operator "%s" is not a postfix operator and does not take a left operand.',
                $this->token,
            ));
        }
    }

    public function isInfix(): bool
    {
        return $this->isPrefix() and $this->isPostfix();
    }
}
