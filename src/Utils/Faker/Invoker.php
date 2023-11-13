<?php

namespace App\Utils\Faker;

trait Invoker
{
    /** @throws \ReflectionException */
    public function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflectionClass = new \ReflectionClass($object::class);

        $method = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
