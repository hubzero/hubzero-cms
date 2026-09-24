<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Logjserrors;

use Hubzero\Module\Module;
use Request;
use Config;

/**
 * Module class for logging JS errors
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if (Request::method() == 'POST')
		{
			// Write to hubzero logs if available.
			// Otherwise fallback to the app logs.
			$path = Config::get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			// A page re-rendered after some other form's POST runs this module
			// too, so only a report carrying every field the script below sends
			// is treated as one; anything else just renders the script.
			$log = array();
			foreach (array('message', 'file', 'line', 'url', 'navigator') as $k)
			{
				if (!isset($_POST[$k]) || !is_scalar($_POST[$k]))
				{
					$log = null;
					break;
				}
				$log[$k] = substr((string) $_POST[$k], 0, 1024);
			}

			if ($log !== null)
			{
				$fh = fopen($path . '/client_error.log', 'a');
				fwrite($fh, json_encode($log));
				fclose($fh);
				exit();
			}
		}

		require $this->getLayoutPath();
	}
}
