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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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