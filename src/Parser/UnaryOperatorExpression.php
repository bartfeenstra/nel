<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Parser;

use Bartfeenstra\Nel\Operator\Operator;
use Bartfeenstra\Nel\ParseError;
use Bartfeenstra\Nel\Type;

final class UnaryOperatorExpression extends OperatorExpression
{
    public function __construct(
        Operator $operator,
        public readonly Expression $operand,
    ) {
        parent::__construct($operator);
        if (Type::BOOLEAN !== $this->operand->type()) {
            throw new ParseError(sprintf(
                'Operator "%s" expects its operand to yield a boolean, but instead it yields %s.',
                $this->operator->token,
                $this->operand->type()->value,
            ));
        }
    }

    public function type(): Type
    {
        return Type::BOOLEAN;
    }
}
