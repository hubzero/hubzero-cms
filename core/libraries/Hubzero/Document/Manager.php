<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

use InvalidArgumentException;
use Exception;

/**
 * Document manager
 *
 * Calls it does not define are passed to the document it manages.
 *
 * @mixin Base
 */
class Manager
{
    /**
     * The array of created "types".
     *
     * @var  array<mixed>
     */
    protected $types = [];

    /**
     * The acurrent type.
     *
     * @var  string
     */
    protected $type = 'html';

    /**
     * Create a new manager instance.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->types = [];
    }

    /**
     * Get a type instance.
     *
     * @param   string|null  $type
     * @param   array<mixed>   $options  Associative array of options
     * @return  ($type is 'feed' ? Type\Feed : Base)
     */
    public function instance($type = null, $options = [])
    {
        $type = $type ?: $this->getType();

        $signature = serialize([$type, $options]);

        // If the given type has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a type created by this name, we'll just return that instance.
        if (!isset($this->types[$signature])) {
            try {
                $document = $this->createType($type, $options);
            } catch (Exception $e) {
                $document = $this->createType('html', $options);
            }

            $this->types[$signature] = $document;
        }

        return $this->types[$signature];
    }

    /**
     * Create a new type instance.
     *
     * @param   string  $type
     * @param   array<mixed>   $options  Associative array of options
     * @return  Base
     * @throws  InvalidArgumentException
     */
    protected function createType($type, $options = [])
    {
        $type = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $type);

        $class = __NAMESPACE__ . '\\Type\\' . ucfirst($type);

        if (!is_a($class, Base::class, true)) {
            throw new InvalidArgumentException("Type [$type] not supported.");
        }

        return new $class($options);
    }

    /**
     * Get the current type
     *
     * @param   mixed  $type
     * @return  static
     */
    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Get the current type
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get all of the created "types".
     *
     * @return  array<mixed>
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Dynamically call the default type instance.
     *
     * @param   string  $method
     * @param   array<mixed>   $parameters
     * @return  mixed
     */
    public function __call($method, $parameters)
    {
        return $this->instance()->$method(...$parameters);
    }
}
