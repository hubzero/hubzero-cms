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
use Hubzero\Console\Command\Utilities\Git;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Repository class
 **/
class Repository implements CommandInterface
{
	/**
	 * Output object, implements the Output interface
	 *
	 * @var object
	 **/
	private $output;

	/**
	 * Arguments object, implements the Argument interface
	 *
	 * @var object
	 **/
	private $arguments;

	/**
	 * Repository management mechanism (i.e. git, packages, etc...)
	 *
	 * @var object
	 **/
	private $mechanism;

	/**
	 * Constructor - sets output and arguments for use by command
	 *
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		$this->output    = $output;
		$this->arguments = $arguments;

		// Try to figure out the mechanism
		if (is_dir(JPATH_ROOT . DS . '.git'))
		{
			$this->mechanism = new Git(JPATH_ROOT);
		}
		else
		{
			$this->output->error('Sorry, this command currently only supports setups managed by GIT');
		}
	}

	/**
	 * Default (required) command - just run check command
	 *
	 * @return void
	 **/
	public function execute()
	{
		if ($this->arguments->getOpt('mechanism'))
		{
			$this->output->addLine($this->mechanism->getName());
		}
		else if ($this->arguments->getOpt('version'))
		{
			$this->output->addLine(\Hubzero\Version\Version::VERSION);
		}
		else
		{
			$this->check();
		}
	}

	/**
	 * Check the current status of the repository
	 *
	 * @return void
	 **/
	public function status()
	{
		$mode    = $this->output->getMode();
		$status  = $this->mechanism->status();
		$message = (!empty($status)) 
			? 'This repository is managed by ' . $this->mechanism->getName() . ' and has the following divergence:'
			: 'This repository is managed by ' . $this->mechanism->getName() . ' and is clean';

		if ($mode != 'minimal')
		{
			$this->output->addLine(
				$message,
				array(
					'color' => 'blue'
				)
			);
		}

		$colorMap = array(
			'added'     => 'green',
			'modified'  => 'yellow',
			'deleted'   => 'cyan',
			'untracked' => 'blue',
			'merged'    => 'red'
		);

		if (is_array($status) && count($status) > 0)
		{
			foreach ($status as $k => $v)
			{
				if (count($v) > 0)
				{
					if ($mode == 'minimal')
					{
						$this->output->addLine(
							array(
								$k => $v
							)
						);
					}
					else
					{
						$this->output->addSpacer();
						$this->output->addLine(ucfirst($k) . ' files:');
						foreach ($v as $file)
						{
							$this->output->addLine(
								$file,
								array(
									'color'       => $colorMap[$k],
									'indentation' => 2
								)
							);
						}
					}
				}
			}
		}
	}

	/**
	 * Repository log
	 *
	 * @return void
	 **/
	public function log()
	{
		$mode      = $this->output->getMode();
		$length    = ($this->arguments->getOpt('length')) ? $this->arguments->getOpt('length') : 20;
		$start     = ($this->arguments->getOpt('start')) ? $this->arguments->getOpt('start') : null;
		$upcoming  = $this->arguments->getOpt('include-upcoming');
		$installed = ($this->arguments->getOpt('exclude-installed')) ? false : true;
		$search    = ($this->arguments->getOpt('search')) ? $this->arguments->getOpt('search') : null;
		$format    = '%an: %s';
		$count     = $this->arguments->getOpt('count');

		if ($mode == 'minimal')
		{
			$format = '%H||%an||%ae||%ad||%s';
		}

		$logs = $this->mechanism->log($length, $start, $upcoming, $installed, $search, $format, $count);

		if ($count)
		{
			$this->output->addLine($logs);
			return;
		}

		if ($mode != 'minimal')
		{
			$output = array();
			foreach ($logs as $log)
			{
				$output[] = array('message'=>$log);
			}

			$this->output->addLinesFromArray($output);
		}
		else
		{
			if (is_array($logs) && count($logs) > 0)
			{
				foreach ($logs as $log)
				{
					$entry = array();
					$parts = explode('||', $log);
					$entry[$parts[0]] = array(
						'name'    => $parts[1],
						'email'   => $parts[2],
						'date'    => $parts[3],
						'subject' => $parts[4]
					);

					$this->output->addLine($entry);
				}
			}
		}
	}

	/**
	 * Check and report whether or not the repository is eligible for upgrade/update
	 *
	 * @return void
	 **/
	public function check()
	{
		if ($this->mechanism->isEligibleForUpdate())
		{
			$this->output->addLine(
				'The repository can be updated. Run \'muse repository update\' to proceed',
				array(
					'color' => 'green'
				)
			);
		}
		else
		{
			$this->output->addLine(
				'The repository is currently ineligible for update. Run \'muse repository status\' for the likely cause',
				array(
					'color' => 'red'
				)
			);
		}
	}

	/**
	 * Update the repository
	 *
	 * @return void
	 **/
	public function update()
	{
		$mode = $this->output->getMode();
		if ($this->mechanism->isEligibleForUpdate())
		{
			if ($this->arguments->getOpt('f'))
			{
				if ($mode != 'minimal')
				{
					$this->output->addLine(
						'Updating the repository...',
						array(
							'color' => 'blue'
						),
						false
					);
				}

				// Create rollback point first
				$this->mechanism->createRollbackPoint();

				// Check whether or not we're allowing fast forward pulls only
				$allowNonFf = $this->arguments->getOpt('allow-non-ff');

				// Now do the update
				$response = $this->mechanism->update(false, $allowNonFf);
				if ($response['status'] == 'success')
				{
					if ($mode != 'minimal')
					{
						$this->output->addLine(
							'complete',
							array(
								'color' => 'green'
							)
						);
					}
				}
				else if ($response['status'] == 'fatal')
				{
					$this->output->addLine(
						strtolower($response['message']),
						array(
							'color' => 'red'
						)
					);
				}
				else
				{
					$this->output->addSpacer();
					$this->output->addRaw($response['raw']);
				}
			}
			else
			{
				$response = $this->mechanism->update();

				if (!empty($response))
				{
					if ($mode != 'minimal')
					{
						$this->output->addLine('The repository is behind by ' . count($response) . ' update(s):');
					}
					$logs = array();
					foreach ($response as $log)
					{
						if ($mode == 'minimal')
						{
							$this->output->addLine($log);
						}
						else
						{
							$logs[] = array(
								'message' => $log,
								'type' => array(
									'indentation' => 2,
									'color'       => 'blue'
								)
							);
						}
					}

					if ($mode != 'minimal')
					{
						$this->output->addLinesFromArray($logs);
					}
				}
				else
				{
					if ($mode != 'minimal')
					{
						$this->output->addLine('The repository is already up-to-date');
					}
				}
			}
		}
		else
		{
			$this->output->error('The repository is currently ineligible for update. Run \'muse repository status\' for the likely cause');
		}
	}

	/**
	 * Rollback to last (or named) checkpoint
	 *
	 * @return void
	 **/
	public function rollback()
	{
		if (!$rollbackPoint = $this->mechanism->getRollbackPoint())
		{
			$this->output->error('There are no rollback points currently available');
		}

		$date = date('M jS, Y \a\t g:i:sa', $rollbackPoint);

		if ($this->output->isInteractive())
		{
			$proceed = $this->output->getResponse('Are you sure you want to rollback to the snapshot taken on ' . $date . '? [y|n]');

			if ($proceed == 'y' || $proceed == 'yes')
			{
				$result = $this->mechanism->rollback($rollbackPoint);

				if ($result)
				{
					$this->output->addLine('complete', 'success');
				}
				else
				{
					$this->output->error('Rollback failed');
				}
			}
			else
			{
				$this->output->addLine('Rollback aborted.', 'warning');
			}
		}
		else
		{
			if ($this->arguments->getOpt('f'))
			{
				$this->mechanism->rollback($rollbackPoint);
			}
			else
			{
				$this->output->addLine('Use the -f option to rollback to snapshot taken on ' . $date);
			}
		}
	}

	/**
	 * Do some repository cleanup
	 *
	 * @return void
	 **/
	public function clean()
	{
		if ($this->output->isInteractive())
		{
			$performed = 0;
			$proceed   = $this->output->getResponse('Do you want to purge all rollback points except the latest? [y|n]');

			if ($proceed == 'y' || $proceed == 'yes')
			{
				$this->mechanism->purgeRollbackPoints();
				$performed++;
			}

			$this->output->addLine("Clean up complete. Performed ({$performed}/1) cleanup operations available.");
		}
		else
		{
			if ($this->arguments->getOpt('purge-rollback-points'))
			{
				$this->mechanism->purgeRollbackPoints();
				$this->output->addLine('Purging rollback points.');
			}
			else
			{
				$this->output->addLine('Please specify which cleanup operations to perform');
			}
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Repository management functions.'
			);
	}
}