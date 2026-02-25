<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Wiki extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Authorization check
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_wiki')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Initiate controller
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'pages');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'pages';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_WIKI_PAGES'),
            \Hubzero\Facades\Route::url('index.php?option=com_wiki'),
            true
        );

        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_WIKI_PLUGINS'),
                \Hubzero\Facades\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=wiki&filter_type=wiki')
            );
        }

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
