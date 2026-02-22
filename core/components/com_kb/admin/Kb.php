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
        if (!\User::authorise('core.manage', 'com_kb')) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Request::getCmd('controller', 'articles');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'articles';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_KB_ARTICLES'),
            \Route::url('index.php?option=com_kb&controller=articles', false),
            $controllerName == 'articles'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_KB_CATEGORIES'),
            \Route::url('index.php?option=com_categories&extension=com_kb', false)
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
