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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Repository;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;

/**
 * Repository class
 **/
class Package extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just run check command
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Installs the package repository
	 *
	 * @museDescription  Installs and/or updates packages required by the composer.lock file
	 * @museArgument     3            The installation environment [development|production]
	 * @museArgument     github-user  The GitHub username to use when configuring the development environment
	 *
	 * @return  void
	 **/
	public function install()
	{
		$configuration = $this->arguments->getOpt(3, 'production');

		$args = [];

		switch ($configuration)
		{
			case 'development':
			case 'staging':
				$args[] = '--prefer-source';
				break;

			case 'production':
			default:
				$args[] = '--no-dev';
				break;
		}

		// Composer install
		if ($this->output->getMode() != 'minimal')
		{
			$this->output->addString('Installing any missing libraries from composer...', 'info');
		}

		$cmd = 'php ' . PATH_CORE . DS . 'bin' . DS . 'composer --working-dir=' . PATH_CORE . ' install ' . implode(' ', $args) . ' 2>&1';
		exec($cmd, $output, $status);

		// Composer install
		if ($this->output->getMode() != 'minimal')
		{
			if ($status === 0)
			{
				$this->output->addLine('complete', 'success');
			}
			else
			{
				$this->output->error('failed');
			}
		}
		else
		{
			if ($status !== 0)
			{
				$this->output->error('Failed to update package repository!');
			}
		}

		if ($configuration == 'development' || $configuration == 'staging')
		{
			$this->configure();
		}

		// Composer install
		if ($this->output->getMode() != 'minimal')
		{
			$this->output->addLine('Installation complete!', 'success');
		}
	}

	/**
	 * Configures the package repository setup for use in a given environment
	 *
	 * @museDescription  Configures the package repository for use in a given environment
	 * @museArgument     github-user  The GitHub username to use when configuring the development environment
	 *
	 * @return  void
	 **/
	public function configure()
	{
		$gitHubUser = $this->arguments->getOpt('github-user', null);

		// Offer suggestion if username wasn't provided
		if (is_null($gitHubUser))
		{
			$gitHubUser = exec('whoami');

			if ($this->output->getMode() != 'minimal')
			{
				$this->output->addLine("Assuming {$gitHubUser} as your GitHub username. To override, please specify the '--github-user' flag", 'info');
			}
		}
		else
		{
			if ($this->output->getMode() != 'minimal')
			{
				$this->output->addLine("Using the provided GitHub username: {$gitHubUser}", 'info');
			}
		}

		// Escape user input
		$gitHubUser = escapeshellarg($gitHubUser);

		// Update GIT config within vendor to point to developer fork of primary repo
		if ($this->output->getMode() != 'minimal')
		{
			$this->output->addLine('Updating the framework repository to point to your GitHub fork', 'success');
		}

		$workTree = PATH_CORE . DS . 'vendor' . DS . 'hubzero' . DS . 'framework';
		$dir      = $workTree . DS . '.git';
		$cmd      = "git --git-dir={$dir} --work-tree={$workTree} remote set-url --push origin git@github.com:{$gitHubUser}/framework.git 2>&1";
		$result   = shell_exec($cmd);
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
				'Repository management functions for composer packages.'
			)
			->addTasks($this);
	}
}