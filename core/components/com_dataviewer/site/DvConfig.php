<?php

/**
 * Global configuration holder for the Dataviewer component.
 *
 * @deprecated  Use ConfigFactory::build() for new code. This class
 *              exists for backward compatibility with legacy code
 *              paths (old Controller, filter classes, etc.).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

use Components\Dataviewer\Site\Helpers\ConfigFactory;

class DvConfig
{
    public static $dv_conf = [];
    public static $com_name = '';
    public static $html_path = '';

    public static function init()
    {
        // Delegate to ConfigFactory for the base config
        static::$dv_conf = ConfigFactory::build();
        static::$com_name = static::$dv_conf['com_name'];
        static::$html_path = static::$dv_conf['html_path'];

        // Legacy constants (used by Html helper and old view classes)
        $comPath = str_replace(PATH_ROOT, '', dirname(__DIR__) . '/site');

        if (!defined('DV_COM')) {
            define('DV_COM', static::$com_name);
        }
        if (!defined('DV_COM_PATH')) {
            define('DV_COM_PATH', $comPath);
        }
        if (!defined('DV_COM_HTML')) {
            define('DV_COM_HTML', DV_COM_PATH . DS . 'assets');
        }
        if (!defined('DV_PATH_HTML')) {
            define('DV_PATH_HTML', dirname(__DIR__) . DS . 'site' . DS . 'assets');
        }
    }
}
