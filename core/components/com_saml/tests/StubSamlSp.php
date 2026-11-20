<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

/**
 * Stand-in for a registered Service Provider: a real ServiceProvider whose
 * columns and attribute map come from the test, not the database
 */
class StubSamlSp extends \Components\Saml\Models\ServiceProvider
{
    /**
     * @var  array<string, mixed>
     */
    private $stubData;
    /**
     * @var  array<int, array<string, string>>|null
     */
    private $stubMap;

    /**
     * @param   array<string, mixed>  $data
     * @param   array<int, array<string, string>>|null  $map  Attribute map, one entry per attribute
     */
    public function __construct($data = [], $map = null)
    {
        parent::__construct();

        $this->stubData = $data;
        $this->stubMap  = $map;
    }

    /**
     * @param   mixed  $key
     * @param   mixed  $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->stubData) ? $this->stubData[$key] : $default;
    }

    /**
     * @return  mixed
     */
    public function attributeMap()
    {
        return $this->stubMap;
    }
}
