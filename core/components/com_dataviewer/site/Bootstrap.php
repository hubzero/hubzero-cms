<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

use Hubzero\Component\AbstractComponent;

class Bootstrap extends AbstractComponent
{
	protected function execute(): void
	{
		DvConfig::init();
		Controller::dispatch();
	}
}
