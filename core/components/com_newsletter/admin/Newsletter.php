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
        if (!\User::authorise('core.manage', 'com_newsletter')) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Instantiate controller
        $controllerName = \Request::getCmd('controller', 'newsletters');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \App::abort(404, \Lang::txt('JERROR_INVALID_CONTROLLER'));
            return;
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Menu items
        $menuItems = array(
            'newsletters'  => \Lang::txt('COM_NEWSLETTER_NEWSLETTERS'),
            'mailings'     => \Lang::txt('COM_NEWSLETTER_MAILINGS'),
            'mailinglists' => \Lang::txt('COM_NEWSLETTER_LISTS'),
            'templates'    => \Lang::txt('COM_NEWSLETTER_TEMPLATES'),
            'tools'        => \Lang::txt('COM_NEWSLETTER_TOOLS'),
            'campaigns'    => \Lang::txt('COM_NEWSLETTER_CAMPAIGNS')
        );

        foreach ($menuItems as $k => $v) {
            $active = (\Request::getCmd('controller', 'newsletters') == $k) ? true : false;
            \Submenu::addEntry($v, \Route::url('index.php?option=com_newsletter&controller=' . $k), $active);
        }

        // Execute controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
