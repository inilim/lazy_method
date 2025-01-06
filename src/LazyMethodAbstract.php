<?php

namespace Inilim\LazyMethod;

abstract class LazyMethodAbstract
{
    protected const NAMESPACE   = '',
        PATH_TO_DIR             = '',
        ALIAS                   = [];

    /**
     * @var array<string,string>
     */
    protected static $exists = [];

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
        self::$exists[static::NAMESPACE] ??= '';

        if (\strpos(self::$exists[static::NAMESPACE] . '|', '|' . $n . '|') !== false) {
            return $fn(...$args);
        }

        $file = static::PATH_TO_DIR . '/' . $n . '.php';

        if (\is_file($file)) {
            require_once $file;

            if (\function_exists($fn)) {
                self::$exists[static::NAMESPACE] .= '|' . $n;
                return $fn(...$args);
            }
        }

        throw new \RuntimeException('Call to undefined method ' . static::NAMESPACE . '\\' . $name);
    }

    /**
     * @internal
     * @param string|string[] $name
     */
    static function __include($name)
    {
        self::$exists[static::NAMESPACE] ??= '';

        foreach ((array)$name as $n) {
            $n = static::ALIAS[$n] ?? $n;
            if (\strpos(self::$exists[static::NAMESPACE] . '|', '|' . $n . '|') === false) {
                require_once(static::PATH_TO_DIR . '/' . $n . '.php');
                self::$exists[static::NAMESPACE] .= '|' . $n;
            }
        }
    }
}
