<?php

namespace Kynda\Container;

use Closure;
use ReflectionNamedType;
use ReflectionParameter;

class Util
{
    public static function arrayWrap($value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    public static function unwrapIfClosure($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    public static function getParameterClassName(ReflectionParameter $parameter) : ?string
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        if (! is_null($class = $parameter->getDeclaringClass())) {
            if ($name === 'self') {
                return $class->getName();
            }

            if ($name === 'parent' && $parent = $class->getParentClass()) {
                return $parent->getName();
            }
        }

        return $name;
    }
}
