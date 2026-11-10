<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a user, which only needs to answer get('id') here
 */
class StubUser
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function get($key, $default = null)
    {
        return $key == 'id' ? $this->id : $default;
    }
}
