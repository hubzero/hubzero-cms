<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Developer extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // permissions check
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_developer')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Make extra sure that controller exists
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'applications');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'applications';
        }

        // Add some submenu items
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_DEVELOPER_APPLICATIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_developer&controller=applications'),
            ($controllerName == 'applications')
        );

        // Build the class name
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        // Instantiate controller
        $component = new $controllerName();
        $component->execute();
    }
}
