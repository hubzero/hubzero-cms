<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Poll extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		// Require the base controller
		require_once __DIR__ . DS . 'controllers' . DS . 'polls.php';

		$controller = new Controllers\Polls();
		$controller->execute();
	}
}
