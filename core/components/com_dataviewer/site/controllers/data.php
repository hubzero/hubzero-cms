<?php

/**
 * Data endpoint controller for the Dataviewer component.
 *
 * Returns filtered data in JSON, CSV, KML, KMZ, or SHP format
 * for DataTables AJAX requests and file downloads.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Controllers;

use Components\Dataviewer\Site\DvConfig;
use Components\Dataviewer\Site\Helpers\ConfigFactory;
use Components\Dataviewer\Site\Helpers\DataQuery;
use Components\Dataviewer\Site\Helpers\ModeInterface;
use Hubzero\Component\SiteController;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

class Data extends SiteController
{
    /**
     * View engines this controller accepts.
     *
     * @var  array
     */
    protected $viewEngines = ['php'];

    /**
     * Database identifier parsed from request.
     *
     * @var  array
     */
    protected $dbId = [];

    /**
     * Mode instance.
     *
     * @var  ModeInterface
     */
    protected $mode;

    /**
     * Component config array.
     *
     * @var  array
     */
    protected $dvConfig = [];

    /**
     * Execute the controller.
     *
     * @return  void
     */
    public function execute()
    {
        // Map legacy 'data' task to the default display task
        $this->registerTask('data', 'display');

        // Build config and resolve mode
        $this->dvConfig = ConfigFactory::build();
        $this->dbId = ConfigFactory::parseDbParam(
            Request::getString('db', '')
        );
        $this->dvConfig['settings']['db_id'] = $this->dbId;
        $this->mode = ConfigFactory::resolveMode($this->dbId);
        $this->mode->getConfig($this->dbId, $this->dvConfig);

        // Sync to DvConfig for backward compat
        DvConfig::$dv_conf = $this->dvConfig;

        parent::execute();
    }

    /**
     * Return filtered data (default task).
     *
     * @return  void
     */
    public function displayTask()
    {
        $dd = $this->mode->getDataDefinition(
            $this->dbId,
            $this->dvConfig
        );

        // Sync config back
        DvConfig::$dv_conf = $this->dvConfig;

        if (!$dd) {
            echo '<p class="error">' . \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_INVALID_REQUEST') . '</p>';
            return;
        }

        if (!$this->authorize($dd)) {
            echo '<p class="error">' . \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_UNAUTHORIZED') . '</p>';
            return;
        }

        // Determine output format
        $format = strtolower(Request::getString('type', 'csv'));
        $filterClass = '\\Components\\Dataviewer\\Site\\Filter\\'
            . ucfirst($format);

        // Build and execute query
        $dbConfig = isset($dd['db']) ? $dd['db'] : $this->dvConfig['db'];
        $driver = DataQuery::createDriver($dbConfig);
        $limit = $this->dvConfig['settings']['limit'] ?? 10;
        $query = new DataQuery($driver, $limit);
        $sql = $query->buildQuery($dd);
        $res = $query->execute($sql, $dd);

        // Apply format filter and output
        $output = $filterClass::filter($res, $dd);
        echo $output;

        // Suppress view rendering
        $this->view = null;
    }

    /**
     * Check user authorization.
     *
     * @param   array  $dd  Data definition with ACL
     * @return  bool
     */
    protected function authorize($dd)
    {
        $allowedUsers = $dd['acl']['allowed_users'] ?? null;
        $isValid = is_array($allowedUsers)
            || $allowedUsers === false
            || $allowedUsers == 'registered';
        if (isset($allowedUsers) && $isValid) {
            $this->dvConfig['acl']['allowed_users'] = $allowedUsers;
        }

        $allowedGroups = $dd['acl']['allowed_groups'] ?? null;
        if (
            isset($allowedGroups)
            && (is_array($allowedGroups) || $allowedGroups === false)
        ) {
            $this->dvConfig['acl']['allowed_groups'] = $allowedGroups;
        }

        $usersAllowed = $this->dvConfig['acl']['allowed_users'];
        $groupsAllowed = $this->dvConfig['acl']['allowed_groups'];

        if (
            ($usersAllowed === false && $groupsAllowed === false)
            || isset($dd['acl']['public'])
        ) {
            return true;
        }

        if (User::isGuest()) {
            return false;
        }

        if (isset($dd['acl']['registered'])) {
            return true;
        }

        if ($usersAllowed == 'registered') {
            return true;
        }

        if (
            is_array($usersAllowed)
            && in_array(User::get('username'), $usersAllowed)
        ) {
            return true;
        }

        if ($groupsAllowed !== false && is_array($groupsAllowed)) {
            $groups = \Hubzero\User\Helper::getGroups(User::get('id'));
            if ($groups && count($groups)) {
                foreach ($groups as $g) {
                    if (in_array($g->cn, $groupsAllowed)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
