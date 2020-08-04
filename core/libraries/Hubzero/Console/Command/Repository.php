<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Config;
use Hubzero\Console\Command\Utilities\Git;
use Hubzero\Filesystem\Adapter\Local;

/**
 * Repository class
 **/
class Repository extends Base implements CommandInterface
{
	/**
	 * Repository management mechanism (i.e. git, packages, etc...)
	 *
	 * @var  object
	 **/
	private $mechanism;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);

		// Overriding default document root?
		$directory = PATH_ROOT;
		if ($this->arguments->getOpt('r'))
		{
			if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r')))
			{
				$directory = rtrim($this->arguments->getOpt('r'), DS);
			}
			else
			{
				$this->output->error('Error: Provided directory is not valid');
			}
		}

		// Try to figure out the mechanism
		if (is_dir($directory . DS . '.git'))
		{
			// Set update source
			$source = Config::get('repository_source_name', null);
			$source = $this->arguments->getOpt('source', $source);

			if (is_null($source) || preg_match('/^[[:alnum:]\-\_\.]*\/[[:alnum:]\-\_\.]*$/', $source))
			{
				$this->mechanism = new Git($directory, $source);
			}
			else
			{
				$this->output->error('Sorry, an invalid update mechanism source was provided.');
			}
		}
		else
		{
			$this->output->error('Sorry, this command currently only supports setups managed by GIT');
		}
	}

	/**
	 * Default (required) command - just run check command
	 *
	 * @return  void
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
			$this->status();
		}
	}

	/**
	 * Check the current status of the repository
	 *
	 * @museDescription  Checks the current status of the repository for upgrade eligibility
	 *
	 * @return  void
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
			'renamed'   => 'yellow',
			'copied'    => 'yellow',
			'untracked' => 'black',
			'unmerged'  => 'red',
			'merged'    => 'blue'
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
	 * @museDescription  Shows the past and pending changelog for the repository
	 *
	 * @return  void
	 **/
	public function log()
	{
		$mode      = $this->output->getMode();
		$length    = ($this->arguments->getOpt('length')) ? (int)$this->arguments->getOpt('length') : 20;
		$start     = ($this->arguments->getOpt('start')) ? (int)$this->arguments->getOpt('start') : null;
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
	 * Update the repository
	 *
	 * @museDescription  Updates the repository, by default performing a dry run
	 *
	 * @return  void
	 **/
	public function update()
	{
		$mode = $this->output->getMode();

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

			// Check status and stash as needed
			if (!$this->mechanism->isClean())
			{
				$this->mechanism->stash();
			}

			// Create rollback point first
			$this->mechanism->createRollbackPoint();

			// Check whether or not we're allowing fast forward pulls only
			$allowNonFf = $this->arguments->getOpt('allow-non-ff');

			// Now do the update
			$response = $this->mechanism->update(false, $allowNonFf);
			if ($response['status'] == 'success')
			{
				// Now, check to see whether or not we need to go ahead and push this merge elsewhere
				if ($ref = $this->arguments->getOpt('git-auto-push-ref', false))
				{
					$response = $this->mechanism->push($ref);
					if ($response['status'] === 'success')
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
					else
					{
						$this->output->addLine(
							strtolower($response['message']),
							array(
								'color' => 'red'
							)
						);
					}
				}
				else
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

				// Also check to see if we need to update packages
				if ($this->arguments->getOpt('install-packages', false))
				{
					App::get('client')->call('repository:package', 'install', new Arguments([]), $this->output);
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

	/**
	 * Rollback to last (or named) checkpoint
	 *
	 * @museDescription  Rolls the repository back to the last checkpoint
	 *
	 * @return  void
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
	 * @museDescription  Performs cleanup operations including deleting automatic tags and stashes (if applicable)
	 *
	 * @return  void
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
				$this->output->addLine('Purging rollback points.');
				$performed++;
			}

			$proceed = $this->output->getResponse('Do you want to purge all stashed changes? [y|n]');

			if ($proceed == 'y' || $proceed == 'yes')
			{
				$this->mechanism->purgeStash();
				$this->output->addLine('Purging repository stash.');
				$performed++;
			}

			$this->output->addLine("Clean up complete. Performed ({$performed}/2) cleanup operations available.");
		}
		else
		{
			$didSomething = false;
			if ($this->arguments->getOpt('purge-rollback-points'))
			{
				$this->mechanism->purgeRollbackPoints();
				$this->output->addLine('Purging rollback points.');
				$didSomething = true;
			}

			if ($this->arguments->getOpt('purge-stash'))
			{
				$this->mechanism->purgeStash();
				$this->output->addLine('Purging repository stash.');
				$didSomething = true;
			}

			if (!$didSomething)
			{
				$this->output->addLine('Please specify which cleanup operations to perform');
			}
		}
	}

	/**
	 * Run syntax checker on changed files
	 *
	 * @museDescription  Verifies the validity of the syntax of any pending changes
	 *
	 * @return  void
	 **/
	public function syntax()
	{
		// Get files
		$status = $this->mechanism->status();
		$files  = (isset($status['added']) || isset($status['modified'])) ? array_merge($status['added'], $status['modified']) : array();

		// Whether or not to scan untracked files
		if (!$this->arguments->getOpt('exclude-untracked'))
		{
			$files = (isset($status['untracked'])) ? array_merge($files, $status['untracked']) : $files;
		}

		// Did we find any files?
		if ($files && count($files) > 0)
		{
			// Base standards directory
			if (!$standards = Config::get('repository_standards_dir'))
			{
				$this->output
				     ->addSpacer()
				     ->addLine('You must specify your standards directory first via:')
				     ->addLine(
						'muse configuration set --repository_standards_dir=/path/to/standards',
						array(
							'indentation' => '2',
							'color'       => 'blue'
						)
					)
					->addSpacer()
					->error("Error: failed to retrieve standards directory.");
			}
			else
			{
				$standards = rtrim($standards, DS) . DS . 'HubzeroCS';
			}

			// See what branch we're on, and set standards directory accordingly
			$branch = $this->mechanism->getMechanismVersionName();
			$branch = str_replace('.', '', $branch);
			if (!is_dir($standards . $branch))
			{
				$this->output->error('A standards directory for the current branch does not exist');
			}

			if ($this->arguments->getOpt('no-linting') && $this->arguments->getOpt('no-sniffing'))
			{
				$this->output->addLine('No sniffing or linting...that means we\'re not doing anything!', 'warning');
			}

			foreach ($files as $file)
			{
				$this->output->addString("Scanning {$file}...");
				$passed = true;
				$base   = $this->mechanism->getBasePath();
				$base   = rtrim($base, DS) . DS;

				// Lint files with php extension
				if (!$this->arguments->getOpt('no-linting'))
				{
					if (substr($file, -4) == '.php')
					{
						$cmd = "php -l {$base}{$file}";
						exec($cmd, $output, $code);
						if ($code !== 0)
						{
							$passed = false;
							$this->output->addLine("failed php linter", array('color'=>'red'));
						}
					}
				}

				// Now run them through PHP code sniffer
				if (!$this->arguments->getOpt('no-sniffing'))
				{
					// Append specific standard (with branchname) to command
					$cmd    = "php " . PATH_CORE . DS . 'bin' . DS . "phpcs --standard={$standards}{$branch}/ruleset.xml -n {$base}{$file}";
					$cmd    = escapeshellcmd($cmd);
					$result = shell_exec($cmd);

					if (!empty($result))
					{
						$passed = false;
						$this->output->addLine($result, array('color'=>'red'));
					}
				}

				// Did it all pass?
				if ($passed)
				{
					$this->output->addLine('clear');
				}
			}
		}
		else
		{
			$this->output->addLine('No files to scan');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Repository management functions.'
			)
			->addTasks($this);
	}

	/**
	 * Call composer
	 *
	 * @return void
	 **/
	public function composer()
	{
		$option = $this->arguments->getOpt('option');
		$task = $this->arguments->getOpt('task');
		$valid_tasks = array("show", "available", "install", "update", "remove", "add");
		if ($option == 'package' && in_array($task, $valid_tasks))
		{
			$newCommand = new \Hubzero\Console\Command\App\Package($this->output, $this->arguments);
			$newCommand->$task();
		}
		if ($option == 'repository' && in_array($task, $valid_tasks))
		{
			$newCommand = new \Hubzero\Console\Command\App\Repository($this->output, $this->arguments);
			$newCommand->$task();
		}
	}

	/**
	 * Call composer
	 *
	 * @return void
	 **/
	public function makeDirectory()
	{
		$path = $this->arguments->getOpt('path');

		$newdir = new Local();
		return $newdir->makeDirectory($path, $mode = 0755, $recursive = false, $force = false);
	}

	/**
	 * Call composer
	 *
	 * @return void
	 **/
	public function rename()
	{
		$currPath = $this->arguments->getOpt('currPath');
		$targetPath = $this->arguments->getOpt('targetPath');

		$moveme = new Local();
		return $moveme->rename($currPath, $targetPath);
	}

	/**
	 * Call composer
	 *
	 * @return void
	 **/
	public function cloneRepo()
	{
		$sourceUrl = $this->arguments->getOpt('sourceUrl');
		$uploadPath = $this->arguments->getOpt('uploadPath');

		$newgit = new Git($uploadPath, $sourceUrl);
		return $newgit->cloneRepo($sourceUrl);
	}

}
