<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
use Composer\Installer;
use Composer\Factory;

/**
 * Composer helper class
 */
class Composer
{
    /**
     * Composer\Composer object
     *
     * @var object
     */
    private static $composer = null;

    /**
     * Composer\Factory object
     *
     * @var object
     */
    private static $factory = null;

    /**
     * Composer\Repository\RepositoryManager object
     *
     * @var object
     */
    private static $repositoryManager = null;

    /**
     * Composer\Repository\WriteableRepositoryInterface object
     *
     * @var object
     */
    private static $localRepository = null;

    /**
     * Array of repositories not including the local repo
     *
     * @var array
     */
    private static $remoteRepositories = null;

    /**
     * Composer\IO\BaseIO object needed to interact with composer
     *
     * @var object
     */
    private static $io = null;

    /**
     * Composer\Installer object to perform installation operations
     *
     * @var object
     */
    private static $installer = null;

    /**
     * Composer\EventDispatcher\EventDispatcher object to dispatch messages to composer
     *
     * @var object
     */
    private static $dispatcher = null;

    /**
     * Composer\Json\JsonFile object to hold a few pieces of information about the composer.json file
     *
     * @var object
     */
    private static $json = null;

    /**
     * Initialize a composer object and set the environment so composer can find its configuration
     * We currently assume PATH_APP for all composer operations
     *
     * @return  bool
     **/
    private static function init()
    {
        //Set environment up for composer
        ini_set('memory_limit', '1024M');
        if (getenv('COMPOSER_HOME') != PATH_APP) {
            putenv('COMPOSER_HOME=' . PATH_APP);
        }
        if (getcwd() != PATH_APP) {
            chdir(PATH_APP);
        }
        // Assume no need for output from composer
        if (!self::$io) {
            self::$io = new NullIO();
        }

        // Set up a composer object
        if (!self::$factory) {
            self::$factory = new Factory();
        }

        if (!self::$composer) {
            self::$composer = self::$factory->createComposer(self::$io, PATH_APP
                . '/composer.json', false, PATH_APP, true);
        }
        return true;
    }

    /**
     * Return the factory, ensuring Composer was set up already
     *
     * @return  object  Composer\Factory object in use
     */
    private static function getFactory()
    {
        self::init();
        return self::$factory;
    }

    /**
     * Return composer object, ensuring it has been set up
     *
     * @return  object  Composer\Composer object representing this composer instance
     */
    private static function getComposer()
    {
        self::init();
        return self::$composer;
    }

    /**
     * Reset and reinitialize Composer.
     * This is required for doing most update and remove operations
     *
     * @return  boolean Indicates success or failure
     */
    private static function resetComposer()
    {
        self::$composer = null;
        self::$factory = null;
        self::$repositoryManager = null;
        self::$localRepository = null;
        self::$remoteRepositories = null;
        self::$io = null;
        self::$installer = null;
        self::$dispatcher = null;
        return self::init();
    }

    /**
     * Return the IO object to interact with composer
     *
     * @return  object  Composer\IO object in use
     */
    private static function getIO()
    {
        self::init();
        return self::$io;
    }

    /**
     * Return the repository manager
     *
     * @return  object  Composer\Repository\RepositoryManager in use
     */
    private static function getRepositoryManager()
    {
        self::init();
        if (!self::$repositoryManager) {
            self::$repositoryManager = self::$composer->getRepositoryManager();
        }
        return self::$repositoryManager;
    }

    /**
     * Return the local(installed) repository
     *
     * @return  object  Composer\Repository\RepositoryInterface containing the local repository
     */
    private static function getLocalRepository()
    {
        self::init();
        if (!self::$localRepository) {
            self::$localRepository = self::getRepositoryManager()->getLocalRepository();
        }
        return self::$localRepository;
    }

    /**
     * Return an array of repositories containing remote packages
     *
     * @return  array   Array of Composer\Repository\RepositoryInterfaces containing remote packages
     */
    private static function getRemoteRepositories()
    {
        self::init();
        if (!self::$remoteRepositories) {
            self::$remoteRepositories = self::getRepositoryManager()->getRepositories();
        }
        return self::$remoteRepositories;
    }

    /**
     * Return the installer after ensuring Composer is set up
     *
     * @return  object  Composer\Installer object in use
     */
    private static function getInstaller()
    {
        self::init();
        if (!self::$installer) {
            self::$installer = Installer::create(self::getIO(), self::getComposer());
        }
        return self::$installer;
    }

    /**
     * Return the dispatcher in use by composer
     *
     * @return  object  Composer\EventDispatcher\EventDispatcher in use
     */
    private static function getDispatcher()
    {
        self::init();
        if (!self::$dispatcher) {
            self::$dispatcher = self::getComposer()->getEventDispatcher();
        }
        return self::$dispatcher;
    }

    /**
     * Return the JSON file object representing the composer.json file in use
     *
     * @return  object  Composer\Json\JsonFile in use
     */
    private static function getComposerJson()
    {
        self::init();
        if (!self::$json) {
            $file = self::getFactory()->getComposerFile();
            self::$json = new JsonFile($file);
        }
        return self::$json;
    }

    /**
     * Dispatch an event to composer
     *
     * @param   string  $command    Command message being dispatched
     * @return  void
     */
    private static function dispatch($command)
    {
        $dispatcher = self::getDispatcher();
        $commandEvent = new CommandEvent(PluginEvents::COMMAND, $command, self::getIO(), self::getIO());
        $dispatcher->dispatch($commandEvent->getName(), $commandEvent);
    }

    /**
     * Require a package/version by manipulating the composer.json file
     *
     * @param       string  $packageName    The package name in the form of vendor/package
     * @param       string  $constraint     The version constraint string
     *                                      See: https://getcomposer.org/doc/articles/versions.md
     * @return  boolean Indicates if the operation succeeded
     */
    private static function requirePackage($packageName, $constraint = 'dev-master')
    {
        if (empty($packageName)) {
            return false;
        }
        self::init();
        $json = self::getComposerJson();
        $contents = file_get_contents($json->getPath());
        $manipulator = new JsonManipulator($contents);
        if (!$manipulator->addLink('require', $packageName, $constraint, false)) {
            return false;
        }
        file_put_contents($json->getPath(), $manipulator->getContents());
        return true;
    }

    /**
     * Unrequire a package by manipulating the composer.json file
     *
     * @param       string  $packageName   The package name in the form of vendor/pacakage
     * @return  boolean Indicates if the operation succeeded
     */
    private static function unrequirePackage($packageName)
    {
        if (empty($packageName)) {
            return false;
        }
        self::init();
        self::dispatch('remove');
        $json = self::getComposerJson();
        $contents = file_get_contents($json->getPath());
        $manipulator = new JsonManipulator($contents);
        if (!$manipulator->removeSubNode('require', $packageName, false)) {
            return false;
        }
        file_put_contents($json->getPath(), $manipulator->getContents());
        return true;
    }

    /**
     * Update a package or list of packages
     *
     * @param       array   $packageName   List of packages to be updated
     * @return  boolean Indicates success or failure
     */
    private static function runUpdate($packageName)
    {
        self::init();
        self::dispatch('update');
        if (is_null($packageName)) {
            $package = [];
        } elseif (is_string($packageName)) {
            $package = [$packageName];
        } else {
            $package = $packageName;
        }
        $installer = self::getInstaller();
        $installer
            ->setUpdate(true)
            ->setUpdateWhitelist($package);
        return $installer->run();
    }

    /**
     * Return Composer's configuration
     *
     * @return  object  Composer\Config object in use by composer
     */
    private static function getConfig()
    {
        self::init();
        return self::$composer->getConfig();
    }

    /**
     * Updates all packages according to their version contraints
     *
     * @return  void
     */
    public static function updatePackages()
    {
        // Send empty array to update all packages
        self::runUpdate([]);
    }

    /**
     * Update a single package to its latest version
     *
     * @param   string  $packageName  Name of the package to update
     * @return  mixed
     */
    public static function updatePackage($packageName)
    {
        return self::runUpdate($packageName);
    }

    /**
     * Install a package as a specific version
     *
     * @param       string  $packageName    Name of the package to install
     * @param       string  $constraint     Version constraint string
     *                                      See: https://getcomposer.org/doc/articles/versions.md
     * @return  boolean Indicates success or frailure
     */
    public static function installPackage($packageName, $constraint = 'dev-master')
    {
        if (self::requirePackage($packageName, $constraint)) {
            self::resetComposer();
            return self::runUpdate($packageName);
        }
        return false;
    }

    /**
     * Remove a package
     *
     * @param       string  $packageName    Name of the package to remove
     * @return  boolean Indicates success or failure
     */
    public static function removePackage($packageName)
    {
        if (self::unrequirePackage($packageName)) {
            self::resetComposer();
            return self::runUpdate($packageName);
        }
        return false;
    }

    /**
     * Get a list of packages that are installed
     *
     * @return  array   Array of Composer\Package\PackageInterface representing locally installed packages
     */
    public static function getLocalPackages()
    {
        $localRepo = self::getLocalRepository();
        return $localRepo->getPackages();
    }

    /**
     * Get a list of remote packages
     *
     * @return  array   Array of Composer\Package\PackageInterface representing packages from remote repositories
     */
    public static function getRemotePackages()
    {
        $remoteRepos = self::getRemoteRepositories();
        $remotePackages = [];
        foreach ($remoteRepos as $repo) {
            if (method_exists($repo, "getRepoConfig")) {
                $config = $repo->getRepoConfig();
                if (is_array($config) && isset($config['type']) && $config['type'] != 'composer') {
                    $packages = $repo->getPackages();
                    foreach ($packages as $package) {
                        $remotePackages[$package->getName()] = $package;
                    }
                }
            }
        }
        return $remotePackages;
    }

    /**
     * Get a list of available packages
     *
     * @return  array   Array of packages that are available in remote repositories but have no
     * locally installed version
     */
    public static function getAvailablePackages()
    {
        $availablePackages = self::getRemotePackages();
        $localPackages = self::getLocalPackages();
        foreach ($localPackages as $installedPackage) {
            unset($availablePackages[$installedPackage->getName()]);
        }
        return $availablePackages;
    }

    /**
     * Get list of remote repositories
     *
     * @return  array   Array of Composer\Repository\PackageRepository that composer can use
     */
    public static function getRepositories()
    {
        $remoteRepos = self::getRemoteRepositories();
        return $remoteRepos;
    }

    /**
     * Get a single repository by URL
     *
     * @param       string  $url    URL of the repository to find
     * @return  mixed       The Composer\Repository\PackageRepository with a URL matching the parameter, or null
     */
    public static function getRepositoryByUrl($url)
    {
        $remoteRepos = self::getRemoteRepositories();
        foreach ($remoteRepos as $repo) {
            $config = $repo->getRepoConfig();
            if ($config['url'] == $url) {
                return $repo;
            }
        }
        return null;
    }

    /**
     * Return the configuration of a repository by its alias
     *
     * @param       string  $alias  Alias of the repository, as found in the composer.json
     * @return  mixed       The Composer\Repository\PackageRepository that matches, or null
     */
    public static function getRepositoryConfigByAlias($alias)
    {
        $config = self::getConfig();
        $repos = self::getRepositoryConfigs();
        if (isset($repos[$alias])) {
            return $repos[$alias];
        }
        return null;
    }

    /**
     * Get repository configurations
     *
     * @return  array   Array of configurations for repositories
     */
    public static function getRepositoryConfigs()
    {
        $config = self::getConfig();
        $repos = $config->getRepositories();
        return $repos;
    }

    /**
     * Find available packages matching the given constraints
     *
     * @param       string  $packageName        Name of the package to be found
     * @param       string  $versionConstraint  Version constraint for package
     *                                          See: https://getcomposer.org/doc/articles/versions.md
     * @return  mixed       Composer\Package\PackageInterface or null
     */
    public static function findRemotePackages($packageName, $versionConstraint)
    {
        $repoManager = self::getRepositoryManager();
        return $repoManager->findPackages($packageName, $versionConstraint);
    }

    /**
     * Find an installed package matching given the constraints
     *
     * @param       string  $packageName        Name of the package to be found
     * @param       string  $versionConstraint  Version constraint for package
     *                                          See: https://getcomposer.org/doc/articles/versions.md
     * @return  mixed       Composer\Package\PackageInterface or null
     */
    public static function findLocalPackages($packageName, $versionConstraint = null)
    {
        $localRepo = self::getLocalRepository();
        return $localRepo->findPackages($packageName, $versionConstraint);
    }

    /**
     * Find an installed package by name alone
     *
     * @param       string  $packageName    Name of the package to be found
     * @return  mixed       Composer\Package\PackageInterface or null
     */
    public static function findLocalPackage($packageName)
    {
        $localRepo = self::getLocalRepository();
        return $localRepo->findPackage($packageName, '*');
    }

    /**
     * Add a repository to the composer.json file
     *
     * @param       string  $alias  The alias for the new repository
     * @param       string  $json       The JSON representing the new repository
     * @return  void
     */
    public static function addRepository($alias, $json)
    {
        $config = self::getConfig();
        $configSource = $config->getConfigSource();
        $value = JsonFile::parseJson($json);
        $configSource->addRepository($alias, $value);
    }

    /**
     * Remove a repository from the composer.json file
     *
     * @param       string  $alias  The alias of the repository to remove
     * @return  void
     */
    public static function removeRepository($alias)
    {
        $config = self::getConfig();
        $configSource = $config->getConfigSource();
        $configSource->removeRepository($alias);
    }
}
