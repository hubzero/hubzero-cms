<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Install client
 */
class Install implements ClientInterface
{
    /**
     * ID
     *
     * @var  integer
     */
    public $id = 3;

    /**
     * Name
     *
     * @var  string
     */
    public $name = 'install';

    /**
     * A url to init this client.
     *
     * @var  string
     */
    public $url = 'install';
}
