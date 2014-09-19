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

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Log class
 **/
class Log extends Base implements CommandInterface
{
	/**
	 * Keep track of log class
	 *
	 * @var string
	 **/
	private $log = null;

	/**
	 * Keep track of prompt character
	 *
	 * @var string
	 **/
	private $promptChar = null;

	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Follow a log
	 *
	 * @return void
	 **/
	public function follow()
	{
		// This really only makes sense in interactive mode...duh
		if (!$this->output->isInteractive())
		{
			$this->output->error('This feature is only available in interactive mode!');
		}

		// Set tty icanon so keystrokes are captured without enter
		// Save settings first so we can reapply them when all is said and done
		exec('stty -g', $settings);
		exec('stty -icanon');

		// Get file to follow and a few other arguments
		$log        = $this->arguments->getOpt(3);
		$filters    = (array)$this->arguments->getOpt('filter');
		$threshold  = $this->arguments->getOpt('threshold');
		$noBeep     = $this->arguments->getOpt('no-beep');
		$prompt     = trim($this->arguments->getOpt('prompt', '>'));
		$dateFormat = $this->arguments->getOpt('date-format', 'normal');

		// Get class for this log type
		$class = __NAMESPACE__ . '\\Log\\' . ucfirst($log);

		// Make sure class exists
		if (!class_exists($class))
		{
			$this->output->error('Log does not current support the provided log type!');
		}
		else
		{
			$path = $class::path();
		}

		// Validate given log path
		if (!is_file($path) || !is_readable($path))
		{
			$this->output->error("{$path} does not appear to be a valid log file");
		}

		// Set log and prompt character
		$this->log = $class;
		$this->promptChar = $prompt;

		// Parse thresholds
		if ($threshold)
		{
			$threshold = $this->parseThresholds($threshold);
		}

		// Parse date format
		$dateFormat = $this->mapDateFormat($dateFormat);

		// Set up pipes
		$descs = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		// Tail the given log
		$proc = proc_open("tail -f {$path}", $descs, $fp);

		// Set streams to be non-blocking so we aren't sitting and waiting
		stream_set_blocking(STDIN, 0);
		stream_set_blocking($fp[1], 0);

		// Print log info/formatting
		$class::format($this->output);

		// Print initial prompt character
		$this->printPrompt();

		// Vars to track state/mode
		$pause = false;
		$input = false;

		// Loop indefinitely
		while (true)
		{
			// Check for new log entries
			$buffer = fgets($fp[1]);

			// If we found one and not paused and not in input mode
			if ($buffer !== false && !$pause && !$input)
			{
				// Parse the log line (log class will handle output)
				$class::parse($buffer, $this->output, array('threshold'=>$threshold, 'noBeep'=>$noBeep, 'dateFormat'=>$dateFormat));

				// Add new prompt character
				$this->printPrompt();
			}

			// Listen for keystrokes from stdin
			if (!$input)
			{
				$char = fgetc(STDIN);
				if ($char !== false)
				{
					$newPrompt = true;

					switch (ord($char))
					{
						// Line feed and carriage return
						case 10:
						case 13:
							// Do nothing
							break;

						// Numbers 0-9 -> toggle individual fields
						case 48:
						case 49:
						case 50:
						case 51:
						case 52:
						case 53:
						case 54:
						case 55:
						case 56:
						case 57:
							$this->output->backspace();
							// Toggle will return true if we have a valid field number
							if ($result = $class::toggle($char))
							{
								$this->output->addLine($result);
							}
							else
							{
								// Unknown field
								$this->output->addLine('Unknown identifier', 'warning');
							}
							break;

						// b - toggle beep
						case 98:
							$this->output->backspace();
							$noBeep = ($noBeep) ? false : true;
							$this->output->addLine('(b)eep ' . (($noBeep) ? 'off' : 'on'));
							break;

						// f - show fields
						case 102:
							$this->output->backspace();
							$class::format($this->output);
							break;

						// h - help info
						case 104:
							$this->output->backspace();
							$help = "q: quit, h: help, i: input mode, p: pause/play, b: beep on/off, f: fields, r: rerender last 100 lines";
							$this->output->addLine($help);
							break;

						// i - input mode
						case 105:
							$this->output->backspace((2+strlen($prompt)));
							$this->output->addLine('You\'re entering input mode. Pausing log streaming. Type "done" or "i" to return.');
							$this->output->addString('input mode >>> ');

							// Set input mode to true and undo icanon setting
							$input = true;
							exec('stty icanon');
							$newPrompt = false;
							break;

						// p - toggle play/pause
						case 112:
							$this->output->backspace();
							$pause = ($pause) ? false : true;
							$this->output->addLine((($pause) ? '(p)ause' : '(p)lay'));
							break;

						// q - quit
						case 113:
							$this->output->backspace();
							$this->output->addLine('(q)uit');

							// Don't just close the process, terminate it!
							proc_terminate($proc);
							break 2;

						// r - rerender last 100 lines
						case 114:
							$this->output->backspace();
							$this->output->addLine('(r)erender last 100 lines');

							// Grab the lines again
							$content = shell_exec("tail -100 {$path}");

							// Split them up and loop through them
							$lines   = explode("\n", trim($content));
							foreach ($lines as $line)
							{
								$this->printPrompt();
								$class::parse($line, $this->output, array('threshold'=>$threshold, 'noBeep'=>$noBeep, 'dateFormat'=>$dateFormat));
							}
							break;

						// Default
						default:
							// Just delete the typed character
							$this->output->backspace(1, true);
							$newPrompt = false;
							break;
					}

					// Should we print a new prompt character?
					if ($newPrompt)
					{
						$this->printPrompt();
					}
				}
			}
			// Input mode
			else
			{
				$string = fgets(STDIN);

				if ($string !== false)
				{
					// Initialize vars
					$string = trim($string);
					$arg    = null;
					$parts  = array();

					// If a space is present, we'll assume some sort of command/arguments scenario
					if (strpos($string, ' '))
					{
						$parts  = explode(' ', $string, 2);
						$string = $parts[0];
						$arg    = isset($parts[1]) ? $parts[1] : null;
					}

					switch ($string)
					{
						// Quit completely
						case 'quit':
							proc_terminate($proc);
							break 2;

						// Set date format
						case 'date':
							// Make sure arg is set
							if (isset($arg))
							{
								$dateFormat = $this->mapDateFormat($arg);
								$this->output->addLine('Date format changed', 'success');
							}

							$this->output->addLine('You can set the date format using: "date [format]". Options include: full, long, normal/default, and short.');
							$this->output->addString('input mode >>> ');
							break;

						// Close input mode
						case 'done':
						case 'i':
							$input = false;
							exec('stty -icanon');
							$this->printPrompt();
							break;

						// Show fields
						case 'fields':
							$this->output->addSpacer();
							$class::format($this->output);
							$this->output->addString('input mode >>> ');
							break;

						// Show or set threshold value(s)
						case 'threshold':
							// If arg is set, we're setting threshold
							if (isset($arg))
							{
								$threshold = $this->parseThresholds($arg);
								$this->output->addLine('Threshold(s) set');
							}
							else
							{
								if ($threshold)
								{
									// Parse threshold array
									$printable = array();
									foreach ($threshold as $key => $value)
									{
										$printable[] = $key . ':' . $value;
									}
									$this->output->addString('Threshold is currently set to ' . implode(', ', $printable) . '. ');
								}
								else
								{
									$this->output->addString('No thresholds are currently set. ');
								}

								$this->output->addLine('You can set thresholds using the format: "threshold field:value[,field:value]"');
							}

							$this->output->addString('input mode >>> ');
							break;

						// Hide/show fields
						case 'show':
						case 'hide':
							// Toggle will return false or string indicating fields changed
							if ($result = $class::toggle($arg, (($string == 'show') ? true : false)))
							{
								$this->output->addLine($result, 'success');
							}
							else
							{
								$this->output->addLine("{$arg} is not an available field", 'warning');
							}

						// Default - just show new prompt
						default:
							$this->output->addString('input mode >>> ');
							break;
					}
				}
			}

			// Sleep for a bit so we don't run away with the CPU
			usleep(50000);
		}

		// Restore tty settings
		exec("stty {$settings[0]}");
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('Log management functions')
		     ->render();
	}

	/**
	 * Parse thresholds from user input to array
	 *
	 * @param  (string) $thresholds
	 * @return (array)  $threshold
	 **/
	private function parseThresholds($thresholds)
	{
		$log = $this->log;

		if (strpos($thresholds, ','))
		{
			$thresholds = explode(',', $thresholds);
		}
		else
		{
			$thresholds = (array)$thresholds;
		}

		$threshold = array();
		foreach ($thresholds as $t)
		{
			$params = explode(':', $t);
			if ($log::isField(trim($params[0])))
			{
				$threshold[trim($params[0])] = trim($params[1]);
			}
		}

		return $threshold;
	}

	/**
	 * Print prompt character + space
	 *
	 * @return void
	 **/
	private function printPrompt()
	{
		$this->output->addString($this->promptChar . ' ');
	}

	/**
	 * Map date format keyword to php date format string
	 *
	 * @return (string) $format
	 **/
	private function mapDateFormat($name)
	{
		switch ($name)
		{
			case 'full':
				$log    = $this->log;
				$format = $log::getDateFormat();
				break;

			case 'long':
				$format = "Y-m-d H:i:s";
				break;

			case 'short':
				$format = "h:i:sa";
				break;

			default:
				$format = "D h:i:sa";
				break;
		}

		return $format;
	}
}