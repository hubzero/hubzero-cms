<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Base\Client;

use Hubzero\Console\Arguments;
use Hubzero\Console\Output;

/**
 * Site client
 */
class Cli implements ClientInterface
{
	/**
	 * ID
	 *
	 * @var  integer
	 */
	public $id = 6;

	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'cli';

	/**
	 * Alias
	 *
	 * @var  string
	 */
	public $alias = 'cli';

	/**
	 * A url to init this client
	 *
	 * @var  string
	 */
	public $url = '';

	/**
	 * Method to call another console command
	 *
	 * @param   string  $class      The command to call
	 * @param   string  $task       The command task to call
	 * @param   object  $arguments  The command arguments
	 * @param   object  $output     The command output
	 * @return  void
	 */
	public function call($class, $task, Arguments $arguments, Output $output)
	{
		// Namespace class
		$class = Arguments::routeCommand($class);

		// Say no to infinite nesting!
		$backtrace = debug_backtrace();
		$previous  = $backtrace[1];
		$prevClass = $previous['class'];
		$prevTask  = $previous['function'];

		if ($prevClass == $class && $prevTask == $task)
		{
			$output->error('You\'ve attempted to enter an infinite loop. We\'ve stopped you. You\'re welcome.');
		}

		// If task is help, set the output to our output class with extra methods for rendering help doc
		if ($task == 'help')
		{
			$output = $output->getHelpOutput();
		}

		$command = new $class($output, $arguments);

		$command->{$task}();
	}
}