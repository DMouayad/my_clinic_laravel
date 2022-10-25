<?php

namespace Support\Helpers;

class ClassNameStringifier
{
    public static function getClassName(object|string $object_or_class): string
    {
        $class = is_string($object_or_class)
            ? $object_or_class
            : get_class($object_or_class);
        return substr(strrchr($class, "\\"), 1);
    }
}
