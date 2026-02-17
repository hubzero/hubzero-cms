<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Mailto\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component bootstrap
 */
class Bootstrap extends AbstractComponent
{
	/**
	 * Entry point
	 *
	 * @return  void
	 */
	protected function execute(): void
	{
		require_once __DIR__ . '/helpers/mailto.php';
		require_once __DIR__ . '/controllers/mailings.php';

		$controller = new Controllers\Mailings();
		$controller->execute();
	}
}
