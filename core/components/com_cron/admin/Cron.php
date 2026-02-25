<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Cron extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_cron')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_CRON_JOBS'),
            \Hubzero\Facades\Route::url('index.php?option=com_cron'),
            true
        );

        require_once dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php';
        if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_CRON_PLUGINS'),
                \Hubzero\Facades\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=cron&filter_type=cron')
            );
        }

        require_once __DIR__ . DS . 'controllers' . DS . 'jobs.php';

        $controller = new Controllers\Jobs();
        $controller->execute();
    }
}
