<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Cache;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Cache command class
 **/
class Css extends Base implements CommandInterface
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
		     ->render();
	}

	/**
	 * Clear Site.css & Site.less.cache files
	 *
	 * @return  void
	 */
	public function clear()
	{
		$cacheDir = PATH_APP . DS . 'cache';
		$files    = array('site.css', 'site.less.cache');

		// Remove each file
		foreach ($files as $file)
		{
			if (!is_file($cacheDir . DS . $file))
			{
				$this->output->addLine($file . ' does not exist', 'warning');
				continue;
			}

			if (!Filesystem::delete($cacheDir . DS . $file))
			{
				$this->output->addLine('Unable to delete cache file: ' . $file, 'error');
			}
			else
			{
				$this->output->addLine($file . ' deleted', 'success');
			}
		}

		// success!
		$this->output->addLine('All CSS cache files removed!', 'success');
	}
}
