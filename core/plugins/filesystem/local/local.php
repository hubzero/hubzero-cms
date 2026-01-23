<?php

// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

require_once __DIR__ . DS . 'LocalAdapter.php';

/**
 * Plugin class for local filesystem connectivity
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgFilesystemLocal extends \Hubzero\Plugin\Plugin
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
