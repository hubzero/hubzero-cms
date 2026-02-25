<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Media extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_media')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $params = \Hubzero\Facades\Component::params('com_media');
        $path = trim($params->get('file_path', 'site/media'), '/');
        $path = $path ? $path . '/' : '';

        define('COM_MEDIA_BASE', PATH_APP . '/' . $path);

        $baseurl = rtrim(\Hubzero\Facades\Request::root(), '/') . substr(COM_MEDIA_BASE, strlen(PATH_ROOT));
        define('COM_MEDIA_BASEURL', $baseurl);

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'media_test');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'media';
        }

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        $controller = new $controllerName();
        $controller->execute();
    }
}
