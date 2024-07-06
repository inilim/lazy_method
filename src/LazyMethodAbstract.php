<?php

namespace Inilim\LazyMethod;

abstract class LazyMethodAbstract
{
    protected const NAMESPACE   = '';
    protected const PATH_TO_DIR = '';
    protected const ALIAS       = [];

    /**
     * @internal desc
     * @param string $name
     * @param mixed[]|array{} $arguments
     * @return mixed|void
     */
    function __call($name, $arguments)
    {
        return self::__callStatic($name, $arguments);
    }

    /**
     * @internal desc
     * @param string $name
     * @param mixed[]|array{} $arguments
     * @return mixed|void
     */
    static function __callStatic($name, $arguments)
    {
        $n = static::ALIAS[$name] ?? $name;
        $fn = static::NAMESPACE . '\\' . $n;
        if (\function_exists($fn)) {
            return $fn(...$arguments);
        }

        $file = static::PATH_TO_DIR . '/' . $n . '.php';

        if (\is_file($file)) {
            require_once $file;

            if (\function_exists($fn)) {
                return $fn(...$arguments);
            }
        }

        throw new \RuntimeException('Call to undefined method ' . static::NAMESPACE . '\\' . $name);
    }
}
