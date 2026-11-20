<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Site client
 */
class Site implements ClientInterface
{
    /**
     * ID
     *
     * @var  integer
     */
    public $id = 0;

    /**
     * Name
     *
     * @var  string
     */
    public $name = 'site';

    /**
     * Alias
     *
     * @var  string
     */
    public $alias = 'site';

    /**
     * A url to init this client.
     *
     * @var  string
     */
    public $url = '';

    /**
     * Boostrap filesystem path
     *
     * @var  string
     */
    public $path = '';
}
