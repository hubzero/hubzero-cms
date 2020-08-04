<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

/**
 * Output class for rendering help doc
 **/
class Help extends Output
{
	/**
	 * Track whether or not help arguments section has been output yet
	 *
	 * @var  bool
	 **/
	private $hasArgumentsSection = false;

	/**
	 * Set variable to forcibly not include arguments section header
	 *
	 * Most likely useful when adding a section to help output, but
	 * never needing an arguments section header to be automatically
	 * generated for you
	 *
	 * @return  $this
	 **/
	public function noArgsSection()
	{
		$this->hasArgumentsSection = true;

		return $this;
	}

	/**
	 * Add help output overview section
	 *
	 * @return  $this
	 **/
	public function addOverview($text)
	{
		$this
			->addSection('Overview')
			->addParagraph(
				$text,
				array(
					'indentation' => 2
				)
			)
			->addSpacer();

		return $this;
	}

	/**
	 * Adds help output tasks section
	 *
	 * @param   object  $command  The command to introspect for tasks
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function addTasks($command)
	{
		$this->addSection('Tasks');

		$class = new \ReflectionClass($command);
		$tasks = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

		if ($tasks && count($tasks) > 0)
		{
			$list = [];
			$max  = 0;

			foreach ($tasks as $task)
			{
				if (!$task->isConstructor() && $task->name != 'execute' && $task->name != 'help')
				{
					$comment     = $task->getDocComment();
					$description = 'no description available';

					// Check for help ignore flag
					preg_match('/@museDescription ([[:alnum:] ,\.()\-\'\/]*)/', $comment, $matched);

					if ($matched && isset($matched[1]))
					{
						$description = trim($matched[1]);
					}

					$list[] = [
						'name'        => $task->name,
						'description' => $description
					];

					$max    = ($max > strlen($task->name)) ? $max : strlen($task->name);
				}
			}

			if (count($list) > 0)
			{
				foreach ($list as $item)
				{
					$this->addString($item['name'], [
						'color'       => 'blue',
						'indentation' => 2
					]);

					$this->addString(str_repeat(' ', ($max - strlen($item['name']))) . '   ');
					$this->addLine($item['description']);
				}
			}
			else
			{
				$this->addLine('There are no tasks available for this command', [
					'color'       => 'red',
					'indentation' => 2
				]);
			}
		}

		$this->addSpacer();

		return $this;
	}

	/**
	 * Add an argument entry to the help doc
	 *
	 * This is helpful in unifying styles used for help doc
	 *
	 * @param   string  $argument  Actual argument
	 * @param   string  $details   Description of what it does
	 * @param   string  $example   Usage example
	 * @param   string  $required  If it's required, we'll style a bit differently
	 * @return  $this
	 **/
	public function addArgument($argument, $details = null, $example = null, $required = false)
	{
		if (!$this->hasArgumentsSection)
		{
			$this->addSection('Arguments');
			$this->hasArgumentsSection = true;
		}

		$this->addLine(
			$argument . (($required) ? ' (*required)' : ''),
			array(
				'color'       => (($required) ? 'red' : 'blue'),
				'indentation' => 2
			)
		);

		if (isset($details))
		{
			$this->addParagraph(
				$details,
				array(
					'indentation' => 4
				)
			);

			if (!isset($example))
			{
				$this->addSpacer();
			}
		}
		if (isset($example))
		{
			$this->addLine(
				$example,
				array(
					'color'       => 'green',
					'indentation' => 4
				)
			)
			->addSpacer();
		}

		return $this;
	}

	/**
	 * Helper method for adding a new section header to helper doc
	 *
	 * @return  $this
	 **/
	public function addSection($text)
	{
		$this->addLine(
			$text . ":",
			array(
				'color'       => 'yellow',
				'indentation' => 0
			)
		);

		return $this;
	}
}
