<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Oaipmh extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		if (!\User::authorise('core.manage', 'com_oaipmh')) {
		    \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		    return;
		}

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
		if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage')) {
		    \Submenu::addEntry(
		        \Lang::txt('COM_OAIPMH_PLUGINS'),
		        \Route::url('index.php?option=com_plugins&view=plugins&filter_folder=oaipmh&filter_type=oaipmh')
		    );
		}

		// Instantiate controller
		$controller = new Controllers\Config();
		$controller->execute();
	}
}
