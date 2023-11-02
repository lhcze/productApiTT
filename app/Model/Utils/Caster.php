<?php

declare(strict_types=1);

namespace App\Model\Utils;

use App\Model\Exception\Runtime\InvalidStateException;

final class Caster
{
    public static function toInt(mixed $value): int
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return intval($value);
        }

        throw new InvalidStateException('Cannot cast to integer');
    }

    public static function toFloat(mixed $value): float
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return floatval($value);
        }

        throw new InvalidStateException('Cannot cast to float');
    }
}
