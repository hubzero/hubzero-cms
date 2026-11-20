<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Administrator client
 */
class Administrator implements ClientInterface
{
    /**
     * ID
     *
     * @var  integer
     */
    public $id = 1;

    /**
     * Name
     *
     * @var  string
     */
    public $name = 'administrator';

    /**
     * Alias
     *
     * @var  string
     */
    public $alias = 'admin';

    /**
     * A url to init this client.
     *
     * @var  string
     */
    public $url = 'admin';

    /**
     * Boostrap filesystem path
     *
     * @var  string
     */
    public $path = '';
}
