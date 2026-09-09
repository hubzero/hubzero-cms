<?php

/**
 * Admin controller dispatcher for the Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

class Controller
{
    public static function dispatch()
    {
        if (!static::authorized()) {
            $errStr = 'Access restricted.';

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
                $errStr = '<p class="error">Not authorized, access is'
                    . ' limited to "<em>' . $group . '</em>"</p>. '
                    . '<h3>Use the Databases component parameters'
                    . ' to change this</h3>';
            }

            print $errStr;
            return;
        }

        // Get the task
        $task = \Hubzero\Facades\Request::getCmd('task', 'list');

        $taskMap = [
            'list' => Tasks\TaskList::class,
            'config' => Tasks\TaskConfig::class,
            'config_current' => Tasks\ConfigCurrent::class,
            'config_update' => Tasks\ConfigUpdate::class,
            'data_definition' => Tasks\DataDefinition::class,
            'data_definition_new' => Tasks\DataDefinitionNew::class,
            'data_definition_remove' => Tasks\DataDefinitionRemove::class,
            'data_definition_update' => Tasks\DataDefinitionUpdate::class,
            'dataview_list' => Tasks\DataviewList::class,
        ];

        if (isset($taskMap[$task])) {
            // Add task JS if exists
            $jsFile = __DIR__ . DS . 'Tasks' . DS . 'html'
                . DS . $task . '.js';
            if (file_exists($jsFile)) {
                $document = \Hubzero\Facades\App::get('document');
                $document->addScript(
                    DB_PATH . DS . 'Tasks' . DS . 'html'
                        . DS . $task . '.js?v=2'
                );
            }
            $taskMap[$task]::execute();
        }
    }

    public static function authorized()
    {
        if (DvConfig::$conf['access_limit_to_group'] === false) {
            return true;
        }

        if (!\Hubzero\Facades\User::isGuest()) {
            $groups = \Hubzero\User\Helper::getGroups(
                \Hubzero\Facades\User::get('id')
            );
            if ($groups && count($groups)) {
                foreach ($groups as $g) {
                    if ($g->cn == DvConfig::$conf['access_limit_to_group']) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
