<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a registered Service Provider
 */
class StubSamlSp
{
    private $data;
    private $map;

    /**
     * @param   array<string, mixed>  $data
     * @param   array<int, array<string, string>>|null  $map  Attribute map, one entry per attribute
     */
    public function __construct($data, $map = null)
    {
        $this->data = $data;
        $this->map  = $map;
    }

    /**
     * @param   mixed  $key
     * @param   mixed  $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * @return  mixed
     */
    public function attributeMap()
    {
        return $this->map;
    }
}
