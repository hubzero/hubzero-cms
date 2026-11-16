<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Minimal stand-in for the component params registry
 */
class StubParams
{
    private $data;

    /**
     * @param   array<string, mixed>  $data
     */
    public function __construct($data)
    {
        $this->data = $data;
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
}
