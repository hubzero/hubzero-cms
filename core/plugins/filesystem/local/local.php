<?php

namespace Plugins\Filesystem\Local;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Plugin class for local filesystem connectivity
 */
class Local extends Plugin
{
    /**
     * Initializes the local filesystem connection
     *
     * @param   array<mixed>   $params  Any connection params needed
     * @return  \Plugins\Filesystem\Local\LocalAdapter
     **/
    public static function init($params = [])
    {
        $path = rtrim($params['path'] ?? '', '/') . '/'
            . (isset($params['subdir']) ? trim($params['subdir'], '/') : '');
        return new LocalAdapter($path);
    }
}
