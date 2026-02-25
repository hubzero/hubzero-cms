<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Kb extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_kb')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'articles');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'articles';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_KB_ARTICLES'),
            \Hubzero\Facades\Route::url('index.php?option=com_kb&controller=articles', false),
            $controllerName == 'articles'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_KB_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_categories&extension=com_kb', false)
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
