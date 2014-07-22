<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Console application class
 **/
class Application
{
	/**
	 * Arguments class instance
	 *
	 * @var object
	 **/
	private $arguments;

	/**
	 * Output class instance
	 *
	 * @var object
	 **/
	private $output;

	/**
	 * Constructor - set our arguments and output properties
	 *
	 * @param  (object) $arguments
	 * @param  (object) $output
	 * @return void
	 **/
	public function __construct(Arguments $arguments, Output $output)
	{
		$this->arguments = $arguments;
		$this->output    = $output;
	}

	/**
	 * Execute the console application
	 *
	 * All we're really doing here is parsing for class and task,
	 * executing the command, and rendering the output.
	 *
	 * @return void
	 **/
	public function execute()
	{
		try
		{
			$this->arguments->parse();
		}
		catch (UnsupportedCommandException $e)
		{
			$this->output->error($e->getMessage());
		}
		catch (UnsupportedTaskException $e)
		{
			$this->output->error($e->getMessage());
		}

		// Check for interactivity flag and set on output accordingly
		if ($this->arguments->getOpt('non-interactive'))
		{
			$this->output->makeNonInteractive();
		}

		// Check for color flag and set on output accordingly
		if ($this->arguments->getOpt('no-colors'))
		{
			$this->output->makeUnColored();
		}

		$class = $this->arguments->get('class');
		$task  = $this->arguments->get('task');

		// If task is help, set the output to our output class with extra methods for rendering help doc
		if ($task == 'help')
		{
			$this->output = $this->output->getHelpOutput();
		}

		// If the format opt is present, try to use the appropriate output subclass
		if ($this->arguments->getOpt('format'))
		{
			$this->output = $this->output->getOutputFormatter($this->arguments->getOpt('format'));
		}

		$command = new $class($this->output, $this->arguments);

		$command->{$task}();

		$this->output->render();
	}

	/**
	 * Method to call another console command
	 *
	 * @return void
	 **/
	public static function call($class, $task, Arguments $arguments, Output $output)
	{
		// Namespace class
		$class = __NAMESPACE__ . '\\Command\\' . ucfirst($class);

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