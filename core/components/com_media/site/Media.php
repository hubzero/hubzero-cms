<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Site;

use Hubzero\Facades\Component;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Component\AbstractComponent;
use Components\Media\Helpers\MediaHelper;

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
        $params = Component::params('com_media');

        // Make sure the user is authorized to view this page
        $asset = Request::getCmd('asset');
        $author = Request::getCmd('author');
        if (
            !$asset
                or !User::authorise('core.edit', $asset)
            &&  !User::authorise('core.create', $asset)
            &&  count(User::getAuthorisedCategories($asset, 'core.create')) == 0
            &&  !(User::get('id') == $author && User::authorise('core.edit.own', $asset))
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Set the path definitions
        $base = PATH_APP . '/' . $params->get('image_path', 'images');
        $baseurl = Request::root() . '/' . $params->get('image_path', 'images');

        MediaHelper::setBase($base, $baseurl);
        define('COM_MEDIA_BASE', $base);
        define('COM_MEDIA_BASEURL', $baseurl);

        Lang::load('com_media', dirname(__DIR__) . '/admin', null, false, true)
        ||  Lang::load('com_media', __DIR__, null, false, true);

        // Load the admin HTML view

        // Make sure the user is authorized to view this page
        // The controller is named as the administrator's views name it
        // (controller=medialist), or before the task as in task=media.upload
        $cmd = Request::getCmd('task', '');
        $controllerName = Request::getCmd('controller', 'media');

        if (strpos($cmd, '.') != false) {
            // We have a defined controller/task pair -- lets split them out
            list($controllerName, $task) = explode('.', $cmd);

            // The controller runs the task by its own name
            Request::setVar('task', $task);
        }

        // The site's controllers (site/controllers) run the administrator's
        // media manager; until they existed this looked for them in vain, so
        // every request here ended in the error below
        $controllerClass = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        if (!is_subclass_of($controllerClass, \Hubzero\Component\ControllerInterface::class)) {
            App::abort(402, Lang::txt('JERROR_INVALID_CONTROLLER_CLASS'));
        }

        // Perform the Request task
        $controller = new $controllerClass();
        $controller->execute();
    }
}
