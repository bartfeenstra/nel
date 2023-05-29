<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Operator;

use Bartfeenstra\Nel\Type;

abstract class Operator
{
    /**
     * @var Array<self>
     */
    private static array $instances = [];

    protected function __construct(
        public readonly string $token,
        public readonly int $precedence,
    ) {
    }

    abstract public function type(): Type;

    public static function get(): self
    {
        if (!in_array(static::class, self::$instances)) {
            /**
             * @psalm-suppress TooFewArguments
             * @psalm-suppress UnsafeInstantiation
             * @phpstan-ignore-next-line
             */
            self::$instances[static::class] = new static();
        }
        return self::$instances[static::class];
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
        ];
    }

    public static function operator(string $token): Operator
    {
        foreach (static::operators() as $operator) {
            if ($token === $operator->token) {
                return $operator;
            }
        }
        throw new \InvalidArgumentException(sprintf('Unknown operator with token "%s".', $token));
    }
}
