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

    public function __construct($data, $map = null)
    {
        $this->data = $data;
        $this->map  = $map;
    }

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    public function attributeMap()
    {
        return $this->map;
    }
}
