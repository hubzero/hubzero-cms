<?php

/**
 * Admin entry point for the Dataviewer component.
 *
 * Dispatches to the appropriate admin controller based on
 * the 'controller' request parameter.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

class Dataviewer extends AbstractComponent
{
    /**
     * Map legacy task names to controller classes for backward
     * compatibility with old URLs that use task= instead of
     * controller=.
     *
     * @var  array
     */
    protected $taskControllerMap = [
        'list'                     => 'databases',
        'config'                   => 'databases',
        'config_current'           => 'databases',
        'config_update'            => 'databases',
        'dataview_list'            => 'dataviews',
        'data_definition'          => 'dataviews',
        'data_definition_new'      => 'dataviews',
        'data_definition_update'   => 'dataviews',
        'data_definition_remove'   => 'dataviews',
    ];

    protected function execute(): void
    {
        DvConfig::init();

        $document = App::get('document');
        $document->addStyleSheet(
            DvConfig::$conf['com_path'] . '/html/smoothness/jquery-ui.css'
        );
        $document->addStyleSheet(
            DvConfig::$conf['com_path'] . '/html/main.css'
        );
        $document->addScript(
            DvConfig::$conf['com_path'] . '/html/main.js'
        );
        $document->setTitle(DvConfig::$conf['app_title']);

        // Determine controller
        $controllerName = Request::getCmd('controller', '');
        $task = Request::getCmd('task', 'list');

        // If no controller param, resolve from task for BC
        if ($controllerName === '') {
            $controllerName = $this->taskControllerMap[$task]
                ?? 'databases';
        }

        // Check authorization
        if (!Controller::authorized()) {
            $this->renderUnauthorized();
            umask(DvConfig::$conf['sys_umask']);
            return;
        }

        $controllerClass = __NAMESPACE__ . '\\Controllers\\'
            . ucfirst(strtolower($controllerName));

        if (!class_exists($controllerClass)) {
            // Fall back to legacy dispatch
            Controller::dispatch();
            umask(DvConfig::$conf['sys_umask']);
            return;
        }

        $controller = new $controllerClass();
        $controller->execute();
        $controller->redirect();

        umask(DvConfig::$conf['sys_umask']);
    }

    /**
     * Show unauthorized access message.
     *
     * @return  void
     */
    protected function renderUnauthorized()
    {
        if (DvConfig::$conf['modes']['db']['enabled']) {
            $group = htmlspecialchars(
                DvConfig::$conf['access_limit_to_group']
            );
            \Hubzero\Facades\Toolbar::title(
                'Databases',
                'databases'
            );
            \Hubzero\Facades\Toolbar::preferences(
                'com_databases',
                '200'
            );
            print '<p class="error">'
                . \Hubzero\Facades\Lang::txt(
                    'COM_DATAVIEWER_ACCESS_NOT_AUTHORIZED',
                    $group
                )
                . '</p>'
                . '<h3>'
                . \Hubzero\Facades\Lang::txt(
                    'COM_DATAVIEWER_ACCESS_CHANGE_PARAMS'
                )
                . '</h3>';
        } else {
            print \Hubzero\Facades\Lang::txt(
                'COM_DATAVIEWER_ACCESS_RESTRICTED'
            );
        }
    }
}
