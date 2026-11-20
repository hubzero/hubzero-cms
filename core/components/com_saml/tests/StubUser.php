<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a user, which only needs to answer get('id') here
 */
class StubUser
{
    /**
     * @var  int|null
     */
    private $id;

    /**
     * @param   int  $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param   mixed  $key
     * @param   mixed  $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return $key == 'id' ? $this->id : $default;
    }
}
