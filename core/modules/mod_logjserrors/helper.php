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

			$log = array();
			array_map(function($k) use(&$log)
			{
				if (!array_key_exists($k, $_POST))
				{
					header('HTTP/1.1 422 Unprocessable Entity');
					exit();
				}
				$log[$k] = $_POST[$k];
			}, array('message', 'file', 'line', 'url', 'navigator'));

			$fh = fopen($path . '/client_error.log', 'a');
			fwrite($fh, json_encode($log));
			fclose($fh);
			exit();
		}

		require $this->getLayoutPath();
	}
}
