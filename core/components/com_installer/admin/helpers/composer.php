<?php 
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @package   hubzero-cms
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Helpers;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;

/**
 * Composer helper class
 */
class ComposerHelper
{
	private static $composer = null;
	private static $factory = null;
	private static $repositoryManager = null;
	private static $localRepository = null;
	private static $remoteRepositories = null;
	private static $io = null;
	private static $installer = null;
	private static $dispatcher = null;

	private static function _init()
	{
		//Set environment up for composer
		if (getenv('COMPOSER_HOME') != PATH_APP)
		{
			putenv("COMPOSER_HOME=" . PATH_APP);
		}
		if (getcwd() != PATH_APP)
		{
			chdir(PATH_APP);
		}
		// Assume no need for output from composer
		if (!self::$io)
		{
			self::$io = new NullIO();
		}

		// Set up a composer object
		if (!self::$factory)
		{
			self::$factory = new \Composer\Factory();
		}

		if (!self::$composer)
		{
			self::$composer = self::$factory->createComposer(self::$io, PATH_APP . '/composer.json', false, PATH_APP, true);
		}
		return true;
	}

	private static function _getFactory()
	{
		self::_init();
		return self::$factory;
	}

	private static function _getComposer()
	{
		self::_init();
		return self::$composer;
	}

	private static function _resetComposer()
	{
		self::$composer = null;
		self::$factory = null;
		self::$repositoryManager = null;
		self::$localRepository = null;
		self::$remoteRepositories = null;
		self::$io = null;
		self::$installer = null;
		self::$dispatcher = null;
		return self::_init();
	}

	private static function _getIO()
	{
		self::_init();
		return self::$io;
	}

	private static function _getRepositoryManager()
	{
		self::_init();
		if (!self::$repositoryManager)
		{
			self::$repositoryManager = self::$composer->getRepositoryManager();
		}
		return self::$repositoryManager;
	}

	private static function _getLocalRepository()
	{
		self::_init();
		if (!self::$localRepository)
		{
			self::$localRepository = self::_getRepositoryManager()->getLocalRepository();
		}
		return self::$localRepository;
	}

	private static function _getRemoteRepositories()
	{
		self::_init();
		if (!self::$remoteRepositories)
		{
			self::$remoteRepositories = self::_getRepositoryManager()->getRepositories();
		}
		return self::$remoteRepositories;
	}

	private static function _getInstaller()
	{
		self::_init();
		if (!self::$installer)
		{
			self::$installer = \Composer\Installer::create(self::_getIO(), self::_getComposer());
		}
		return self::$installer;
	}

	private static function _getDispatcher()
	{
		self::_init();
		if (!self::$dispatcher)
		{
			self::$dispatcher = self::_getComposer()->getEventDispatcher();
		}
		return self::$dispatcher;
	}

	private static function _dispatch($command)
	{
		$dispatcher = self::_getDispatcher();
		$commandEvent = new CommandEvent(PluginEvents::COMMAND, $command, self::_getIO(), self::_getIO());
		$dispatcher->dispatch($commandEvent->getName(), $commandEvent);
	}

	private static function _requirePackage($packageName, $constraint = 'dev-master')
	{
		if (empty($packageName))
		{
			return false;
		}
		self::_init();
		$file = self::_getFactory()->getComposerFile();
		$json = new JsonFile($file);
		$contents = file_get_contents($json->getPath());
		$manipulator = new JsonManipulator($contents);
		if (!$manipulator->addLink("require", $packageName, $constraint, false))
		{
			return false;
		}

		file_put_contents($json->getPath(), $manipulator->getContents());
		return true;
	}

	private static function _updatePackage($packageName)
	{
		self::_init();
		self::_dispatch('update');
		if (is_null($packageName))
		{
			$package = array();
		}
		elseif (is_string($packageName))
		{
			$package = array($packageName);
		}
		else
		{
			$package = $packageName;
		}
		$installer = self::_getInstaller();
		$installer
			->setUpdate(true)
			->setUpdateWhitelist($package);
		return $installer->run();
	}

	private static function _getConfig()
	{
		self::_init();
		return self::$composer->getConfig();
	}

	public static function updatePackages()
	{
		// Send empty array to update all packages
		self::_updatePackage(array());
	}

	public static function installPackage($packageName, $constraint = 'dev-master')
	{
		if (self::_requirePackage($packageName, $constraint))
		{
			self::_resetComposer();
			return self::_updatePackage($packageName);
		}
		return false;
	}

	public static function getLocalPackages()
	{
		$localRepo = self::_getLocalRepository();
		return $localRepo->getPackages();
	}

	public static function getRemotePackages()
	{
		$remoteRepos = self::_getRemoteRepositories();
		$remotePackages = array();
		foreach ($remoteRepos as $repo)
		{
			$packages = $repo->getPackages();
			foreach ($packages as $package)
			{
				$remotePackages[$package->getName()] = $package;
			}
		}
		return $remotePackages;
	}

	public static function getAvailablePackages()
	{
		$availablePackages = self::getRemotePackages();
		$localPackages = self::getLocalPackages();
		foreach ($localPackages as $installedPackage)
		{
			unset($availablePackages[$installedPackage->getName()]);
		}
		return $availablePackages;
	}

	public static function getRepositories()
	{
		$remoteRepos = self::_getRemoteRepositories();
		return $remoteRepos;
	}

	public static function getRepositoryByUrl($url)
	{
		$remoteRepos = self::_getRemoteRepositories();
		foreach ($remoteRepos as $repo)
		{
			$config = $repo->getRepoConfig();
			if ($config['url'] == $url)
			{
				return $repo;
			}
		}
		return null;
	}

	public static function getRepositoryConfigByAlias($alias)
	{
		$config = self::_getConfig();
		$repos = self::getRepositoryConfigs();
		if (isset($repos[$alias]))
		{
			return $repos[$alias];
		}
		return null;
	}

	public static function getRepositoryConfigs()
	{
		$config = self::_getConfig();
		$repos = $config->getRepositories();
		return $repos;
	}

	public static function findRemotePackages($packageName, $versionConstraint)
	{
		$repoManager = self::_getRepositoryManager();
		return $repoManager->findPackages($packageName, $versionConstraint);
	}

	public static function findLocalPackages($packageName, $versionConstraint = null)
	{
		$localRepo = self::_getLocalRepository();
		return $localRepo->findPackages($packageName, $versionConstraint);
	}

	public static function findLocalPackage($packageName)
	{
		$localRepo = self::_getLocalRepository();
		return $localRepo->findPackage($packageName, '*');
	}

	public static function addRepository($alias, $json)
	{
		$config = self::_getConfig();
		$configSource = $config->getConfigSource();
		$value = JsonFile::parseJson($json);
		$configSource->addRepository($alias, $value);
	}

	public static function removeRepository($alias)
	{
		$config = self::_getConfig();
		$configSource = $config->getConfigSource();
		$configSource->removeRepository($alias);
	}
}
