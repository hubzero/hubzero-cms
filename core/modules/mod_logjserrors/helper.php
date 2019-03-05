<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
