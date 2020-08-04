<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
