<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Composer as ComposerHelper;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Event;
use Route;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

/**
 * Packages Controller
 */
class Packages extends AdminController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        if (!is_file(PATH_APP . '/composer.json')) {
            $view = new \Hubzero\Component\View([
                'base_path' => dirname(__DIR__),
                'name'      => 'warnings',
                'layout'    => 'composer',
            ]);
            $view->display();
            return;
        }

        parent::execute();
    }

    /**
     * Display a list of uninstalled extensions
     *
     * @return  void
     */
    public function displayTask()
    {
        // Paging
        $filters = [
            'limit' => Request::getState(
                $this->option . '.' . $this->controller . '.limit',
                'limit',
                Config::get('list_limit'),
                'int',
            ),
            'start' => Request::getState(
                $this->option . '.' . $this->controller . '.limitstart',
                'limitstart',
                0,
                'int',
            ),
            // Sorting
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'created',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'DESC',
            ),
        ];

        try {
            $packages = ComposerHelper::getLocalPackages();
        } catch (\Exception $e) {
            Notify::error($e->getMessage());
            $packages = [];
        }

        // Output the HTML
        $this->view
            ->set('filters', $filters)
            ->set('total', count($packages))
            ->set('packages', $packages)
            ->display();
    }

    /**
     * Edit or create a new package
     *
     * @return  void
     */
    public function editTask()
    {
        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        Request::setVar('hidemainmenu', 1);

        $packageName = Request::getString('packageName', '');

        try {
            $versions = ComposerHelper::findRemotePackages($packageName, '*');
            $installedPackage = ComposerHelper::findLocalPackage($packageName);
        } catch (\Exception $e) {
            $versions = [];
            $installedPackage = null;
            $this->setError($e->getMessage());
        }

        $this->view
            ->set('packageName', $packageName)
            ->set('installedPackage', $installedPackage)
            ->set('versions', $versions)
            ->setErrors($this->getErrors())
            ->display();
    }

    /**
     * Perform install
     *
     * @return  void
     */
    public function installTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $packageName    = Request::getString('packageName', null);
        $packageVersion = Request::getString('packageVersion', null);

        ComposerHelper::installPackage($packageName, $packageVersion);

        // Set the redirect
        $this->cancelTask();
    }

    /**
     * Add a package to track
     *
     * @return  void
     */
    public function addTask()
    {
        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        Request::setVar('hidemainmenu', 1);

        try {
            $availablePackages = ComposerHelper::getAvailablePackages();
        } catch (\Exception $e) {
            $availablePackages = [];

            $this->setError($e->getMessage());
        }

        $this->view
            ->set('availablePackages', $availablePackages)
            ->setErrors($this->getErrors())
            ->display();
    }

    /**
     * Remove a package
     *
     * @return  void
     */
    public function removeTask()
    {
        Request::checkToken();

        if (!User::authorise('core.delete', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $packages = Request::getArray('packages', []);

        foreach ($packages as $package) {
            ComposerHelper::removePackage($package);

            Event::trigger('onPackageAfterDelete', [$package]);
        }

        $this->cancelTask();
    }
}
