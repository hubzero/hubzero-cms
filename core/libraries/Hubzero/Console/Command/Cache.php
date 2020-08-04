<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
