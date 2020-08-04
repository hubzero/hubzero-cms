<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

/**
 * Scaffolding class for templates
 *
 * @museIgnoreHelp
 **/
class Template extends Scaffolding
{
	/**
	 * Copy and rename template
	 *
	 * @return  void
	 **/
	public function doCopy()
	{
		$from = null;
		$to   = null;

		if ($this->arguments->getOpt(4) && $this->arguments->getOpt(5) && $this->arguments->getOpt(6))
		{
			if ($this->arguments->getOpt(5) == 'to')
			{
				$from = strtolower($this->arguments->getOpt(4));
				$to   = strtolower($this->arguments->getOpt(6));
			}
			else if ($this->arguments->getOpt(5) == 'from')
			{
				$to   = strtolower($this->arguments->getOpt(4));
				$from = strtolower($this->arguments->getOpt(6));
			}
		}
		else
		{
			// If name wasn't provided, and we're in interactive mode...ask for it
			if ($this->output->isInteractive())
			{
				$from = $this->output->getResponse('What template to you want to use as the source?');
				$to   = $this->output->getResponse('What do you want to call the new template?');
			}
			else
			{
				$this->output->error("Error: please provide the source template and destination template name");
			}
		}

		// Normalize source and destination
		$to       = trim($to, DS);
		$from     = trim($from, DS);
		$pathTo   = PATH_CORE;
		$pathFrom = PATH_CORE;

		preg_match('/(core|app)\/([[:alnum:]_-]*)/', $to, $matchesTo);
		preg_match('/(core|app)\/([[:alnum:]_-]*)/', $from, $matchesFrom);

		if (isset($matchesTo[0]))
		{
			$to = $matchesTo[2];

			if ($matchesTo[1] == 'app')
			{
				$pathTo = PATH_APP;
			}
		}
		if (isset($matchesFrom[0]))
		{
			$from = $matchesFrom[2];

			if ($matchesFrom[1] == 'app')
			{
				$pathFrom = PATH_APP;
			}
		}

		// Make sure template doesn't already exist
		if (is_dir($pathTo . DS . 'templates' . DS . $to))
		{
			$this->output->error("Error: the template destination alread exists.");
		}
		if (!is_dir($pathFrom . DS . 'templates' . DS . $from))
		{
			$this->output->error("Error: the template source does not appear to exist.");
		}

		// Make component
		$this->addTemplateFile($pathFrom . DS . 'templates' . DS . $from, $pathTo . DS . 'templates' . DS . $to, true)
			 ->addTemplateFile($pathFrom . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $from . '.ini', $pathTo . DS . 'language' . DS . 'en-GB' . DS . 'en-GB.tpl_' . $to . '.ini', true)
			 ->addReplacement(strtoupper($from), strtoupper($to))
			 ->addReplacement(ucfirst($from), ucfirst($to))
			 ->addReplacement($from, $to)
			 ->doBlindReplacements()
			 ->make();
	}

	/**
	 * Help doc for component scaffolding class
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Scaffolding for templates'
			);
	}
}
