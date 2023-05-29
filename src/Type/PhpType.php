<?php

declare(strict_types=1);

namespace Bartfeenstra\Nel\Type;

enum PhpType: string
{
    case MIXED = 'mixed';
    case INTEGER = 'integer';
    case STRING = 'string';
    case NULL = 'null';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
}
