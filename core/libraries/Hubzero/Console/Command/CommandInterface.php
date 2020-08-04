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
 * Command interface - all commands should implement this interface
 **/
interface CommandInterface
{
	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments);

	/**
	 * All commands are expected to have at least one task, execute.
	 * If no task is given, this is the task that will be run.
	 * It's ok if this task just calls the help command.
	 *
	 * @return  void
	 **/
	public function execute();

	/**
	 * All commands are also expected to have some form of help documentation.
	 *
	 * @return  void
	 **/
	public function help();
}
