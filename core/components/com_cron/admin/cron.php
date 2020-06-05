<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Admin;

if (!\User::authorise('core.manage', 'com_cron'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

\Submenu::addEntry(
	\Lang::txt('COM_CRON_JOBS'),
	\Route::url('index.php?option=com_cron'),
	true
);

require_once dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php';
if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_CRON_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=cron&filter_type=cron')
	);
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'job.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once __DIR__ . DS . 'controllers' . DS . 'jobs.php';

$controller = new Controllers\Jobs();
$controller->execute();
