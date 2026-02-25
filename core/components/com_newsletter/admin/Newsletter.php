<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Newsletter extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_newsletter')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Instantiate controller
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'newsletters');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_INVALID_CONTROLLER'));
            return;
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Menu items
        $menuItems = array(
            'newsletters'  => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_NEWSLETTERS'),
            'mailings'     => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_MAILINGS'),
            'mailinglists' => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_LISTS'),
            'templates'    => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_TEMPLATES'),
            'tools'        => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_TOOLS'),
            'campaigns'    => \Hubzero\Facades\Lang::txt('COM_NEWSLETTER_CAMPAIGNS')
        );

        foreach ($menuItems as $k => $v) {
            $active = (\Hubzero\Facades\Request::getCmd('controller', 'newsletters') == $k) ? true : false;
            \Hubzero\Facades\Submenu::addEntry($v, \Hubzero\Facades\Route::url('index.php?option=com_newsletter&controller=' . $k), $active);
        }

        // Execute controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
