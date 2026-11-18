<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a user record: a real User whose fields come from the test
 */
class StubSamlUser extends \Hubzero\User\User
{
    /**
     * @var  array<string, mixed>
     */
    private $stubData;

    /**
     * @param   array<string, mixed>  $data
     */
    public function __construct($data = [])
    {
        parent::__construct();

        $this->stubData = $data;
    }

    /**
     * @param   mixed  $key
     * @param   string  $default
     * @return  mixed
     */
    public function get($key, $default = '')
    {
        return array_key_exists($key, $this->stubData) ? $this->stubData[$key] : $default;
    }
}
