<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Admin;

if (!\User::authorise('core.manage', 'com_oaipmh'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once __DIR__ . DS . 'controllers' . DS . 'config.php';

$task = \Request::getCmd('task');

\Submenu::addEntry(
	\Lang::txt('COM_OAIPMH_ABOUT'),
	\Route::url('index.php?option=com_oaipmh'),
	(!$task || $task == 'display')
);
\Submenu::addEntry(
	\Lang::txt('COM_OAIPMH_SCHEMAS'),
	\Route::url('index.php?option=com_oaipmh&task=schemas'),
	($task == 'schemas')
);
require_once dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php';
if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_OAIPMH_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=oaipmh&filter_type=oaipmh')
	);
}

// Instantiate controller
$controller = new Controllers\Config();
$controller->execute();
