<?php

namespace Inilim\LazyMethod;

abstract class LazyMethodAbstract
{
    protected const NAMESPACE = '';
    /**
     * @var object[]|array{}
     */
    protected static $instance = [];

    /**
     * @param string $name
     * @param mixed[]|array{} $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return self::__callStatic($name, $arguments);
    }

    /**
     * @param string $name
     * @param mixed[]|array{} $arguments
     * @return mixed|void
     */
    public static function __callStatic($name, $arguments)
    {
        $class = static::NAMESPACE . '\\' . \ucfirst($name);

        if (isset(self::$instance[$class])) {
            return self::getInstance($class)->__invoke(...$arguments);
        } elseif (!\class_exists($class) && !method_exists($class, '__invoke')) {
            throw new \RuntimeException('Call to undefined method ' . static::NAMESPACE . '::' . $name);
        }

        return self::getInstance($class)->__invoke(...$arguments);
    }

    /**
     * @param class-string $class
     */
    protected static function getInstance($class): object
    {
        return self::$instance[$class] ??= new $class;
    }
}
