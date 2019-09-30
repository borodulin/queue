<?php

declare(strict_types=1);

namespace Borodulin\Queue\Serializer;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Trait SerializePropertiesTrait
 * @package Borodulin\Queue\Serializer
 * @see \Serializable
 */
trait SerializePropertiesTrait
{
    /**
     * Setup properties filter to serializer
     * @var int
     * @see ReflectionProperty::IS_PUBLIC
     * @see ReflectionProperty::IS_PROTECTED
     * @see ReflectionProperty::IS_PRIVATE
     * @see ReflectionProperty::IS_STATIC
     */
    protected $modifiersFilter = ReflectionProperty::IS_PUBLIC;

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     * @throws ReflectionException
     */
    public function serialize()
    {
        $properties = [];
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties($this->modifiersFilter) as $property) {
            $properties[$property->name] = $property->getValue($this);
        }
        return serialize($properties);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @throws ReflectionException
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $properties = unserialize($serialized);
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties($this->modifiersFilter) as $property) {
            if (isset($properties[$property->name])) {
                $property->setValue($this, $properties[$property->name]);
            }
        }
    }
}
