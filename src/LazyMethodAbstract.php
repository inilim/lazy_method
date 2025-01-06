<?php

namespace Inilim\LazyMethod;

abstract class LazyMethodAbstract
{
    protected const NAMESPACE   = '',
        PATH_TO_DIR             = '',
        ALIAS                   = [];

    /**
     * @var string
     */
    protected static $exists = '';

    /**
     * @internal desc
     * @param string $name
     * @param mixed[]|array{} $args
     * @return mixed|void
     */
    function __call($name, $args)
    {
        return self::__callStatic($name, $args);
    }

    /**
     * @internal desc
     * @param string $name
     * @param mixed[]|array{} $args
     * @return mixed|void
     */
    static function __callStatic($name, $args)
    {
        $n = static::ALIAS[$name] ?? $name;
        $fn = static::NAMESPACE . '\\' . $n;

        if (\str_contains(self::$exists . '|', '|' . $n . '|')) {
            return $fn(...$args);
        }

        $file = static::PATH_TO_DIR . '/' . $n . '.php';

        if (\is_file($file)) {
            require_once $file;

            if (\function_exists($fn)) {
                self::$exists .= '|' . $n;
                return $fn(...$args);
            }
        }

        throw new \RuntimeException('Call to undefined method ' . static::NAMESPACE . '\\' . $name);
    }

    /**
     * @param string|string[] $name
     */
    static function __include($name)
    {
        foreach ((array)$name as $n) {
            $n = static::ALIAS[$n] ?? $n;
            if (\str_contains(self::$exists . '|', '|' . $n . '|')) {
                require_once(static::PATH_TO_DIR . '/' . $n . '.php');
                self::$exists .= '|' . $n;
            }
        }
    }
}
