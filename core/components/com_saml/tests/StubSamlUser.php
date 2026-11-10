<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a user record
 */
class StubSamlUser
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get($key, $default = '')
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
}
