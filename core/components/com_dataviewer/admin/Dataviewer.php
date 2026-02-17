<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

use Hubzero\Component\AbstractComponent;

class Dataviewer extends AbstractComponent
{
	protected function execute(): void
	{
		DvConfig::init();

		$document = \App::get('document');
		$document->addCustomTag('<meta name="csrf-token" content="' . DB_RID . '" />');
		$document->addStyleSheet(DB_PATH . '/html/smoothness/jquery-ui.css');
		$document->addStyleSheet(DB_PATH . '/html/main.css');
		$document->addScript(DB_PATH . '/html/main.js');
		$document->setTitle(DvConfig::$conf['app_title']);

		Controller::dispatch();

		umask(DvConfig::$conf['sys_umask']);
	}
}
