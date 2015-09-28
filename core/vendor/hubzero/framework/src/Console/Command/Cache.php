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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Cache command class
 **/
class Cache extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('Cache Management')
		     ->addTasks($this)
		     ->render();
	}

	/**
	 * Clear all Cache
	 *
	 * @museDescription  Clears all cached items in document root cache directory
	 *
	 * @return  void
	 */
	public function clear()
	{
		// Path to cache folder
		$cacheDir = PATH_APP . DS . 'cache' . DS . '*';

		// Remove recursively
		foreach (glob($cacheDir) as $cacheFileOrDir)
		{
			$readable = str_replace(PATH_APP . DS, '', $cacheFileOrDir);
			if (is_dir($cacheFileOrDir))
			{
				if (!Filesystem::deleteDirectory($cacheFileOrDir))
				{
					$this->output->addLine('Unable to delete cache directory: ' . $readable, 'error');
				}
				else
				{
					$this->output->addLine($readable . ' deleted', 'success');
				}
			}
			else
			{
				// Don't delete index.html
				if ($cacheFileOrDir != PATH_APP . DS . 'cache' . DS . 'index.html')
				{
					if (!Filesystem::delete($cacheFileOrDir))
					{
						$this->output->addLine('Unable to delete cache file: ' . $readable, 'error');
					}
					else
					{
						$this->output->addLine($readable . ' deleted', 'success');
					}
				}
			}
		}

		$this->output->addLine('Clear cache complete', 'success');
	}
}