<?php

namespace Plugins\Filesystem\Local;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     * @param   array   $params  Any connection params needed
     * @return  object
     **/
    public static function init($params = [])
    {
        $path = rtrim($params['path'] ?? '', '/') . '/'
            . (isset($params['subdir']) ? trim($params['subdir'], '/') : '');
        return new LocalAdapter($path);
    }
}
