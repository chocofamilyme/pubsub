<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    /**
     * @param       $object
     * @param       $methodName
     * @param array $parameters
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }


    /**
     * @param $object
     * @param $propertyName
     * @param $value
     *
     * @throws \ReflectionException
     */
    public function invokeProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionProperty(get_class($object), $propertyName);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
