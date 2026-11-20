<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * API client
 */
class Api implements ClientInterface
{
    /**
     * ID
     *
     * @var  integer
     */
    public $id = 4;

    /**
     * Name
     *
     * @var  string
     */
    public $name = 'api';

    /**
     * A url to init this client.
     *
     * @var  string
     */
    public $url = 'api';

    /**
     * Boostrap filesystem path
     *
     * @var  string
     */
    public $path = '';
}
