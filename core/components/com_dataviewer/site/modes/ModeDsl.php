<?php

/**
 * Project Dataset mode for the Dataviewer component.
 *
 * Resolves database connections and data definitions from
 * project databases plugin configuration.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Modes;

use Components\Dataviewer\Site\DvConfig;
use Components\Dataviewer\Site\Helpers\DataQuery;
use Components\Dataviewer\Site\Helpers\ModeInterface;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

class ModeDsl implements ModeInterface
{
    /**
     * Get database connection config for this mode.
     *
     * @param   array  $dbId    Database identifier array
     * @param   array  $config  Base config array (modified by reference)
     * @return  array  Updated config array
     */
    public function getConfig(array $dbId, array &$config): array
    {
        $params = \Hubzero\Facades\Plugin::params('projects', 'databases');
        $config['db']['host'] = $params->get('db_host');
        $config['db']['user'] = $params->get('db_ro_user');
        $config['db']['password'] = $params->get('db_ro_password');

        return $config;
    }

    /**
     * Get the data definition for the requested dataview.
     *
     * @param   array       $dbId     Database identifier array
     * @param   array       $config   Config array (may be modified)
     * @param   string|null $dvIdArg  Optional explicit dv ID
     * @param   int|null    $versionArg  Optional explicit version
     * @return  array|null  Data definition array, or null if not found
     */
    public function getDataDefinition(
        array $dbId,
        array &$config,
        ?string $dvIdArg = null,
        ?int $versionArg = null
    ): ?array {
        $dd = null;
        $db = \Hubzero\Facades\App::get('db');

        $dvId = $dvIdArg ?? Request::getString('dv');
        $version = $versionArg ?? Request::getInt('v', 0);
        $name = $dvId;

        // Curators
        $curator = '';
        $curatorGroups = [];

        if (!$version) {
            $sql = 'SELECT data_definition FROM `#__project_databases`'
                . ' WHERE `database_name` = ' . $db->quote($name);
            $db->setQuery($sql);
            $database = $db->loadAssoc();

            if ($database === null) {
                return null;
            }

            $dd = json_decode($database['data_definition'] ?? '', true);
        } else {
            $sql = 'SELECT data_definition FROM #__project_database_versions'
                . ' WHERE database_name=' . $db->quote($name)
                . ' AND version=' . $db->quote($version);
            $db->setQuery($sql);
            $ver = $db->loadAssoc();

            if ($ver === null) {
                return null;
            }

            $dd = json_decode($ver['data_definition'], true);

            // Check publication state
            $sql = 'SELECT state, curator FROM #__publication_versions '
                . 'LEFT JOIN #__publication_attachments ON '
                . '(#__publication_versions.publication_id'
                . '=#__publication_attachments.publication_id '
                . 'AND #__publication_versions.id'
                . '=#__publication_attachments.publication_version_id) '
                . 'WHERE object_name=' . $db->quote($name)
                . ' AND object_revision=' . $db->quote($version);
            $db->setQuery($sql);
            $pubVersion = $db->loadAssoc();

            $state = $pubVersion['state'];
            $dd['version'] = $version;
            $dd['publication_state'] = $state;

            if ($state != 1) {
                $curationEnabled = \Hubzero\Facades\Component::params(
                    'com_publications'
                )->get('curation');

                $curatorGroup = trim(
                    \Hubzero\Facades\Component::params(
                        'com_publications'
                    )->get('curatorgroup')
                );

                if ($curationEnabled && $curatorGroup != '') {
                    $curatorGroups[] = $curatorGroup;
                }

                $sql = "SELECT cn FROM #__xgroups g "
                    . "LEFT JOIN #__publication_master_types t "
                    . "ON (g.gidNumber = t.curatorgroup) "
                    . "WHERE t.type = 'Databases'";
                $db->setQuery($sql);
                $dslCurators = $db->loadResult();

                if ($curationEnabled && $dslCurators != '') {
                    $curatorGroups[] = $dslCurators;
                }

                if ($curationEnabled && isset($pubVersion['curator'])
                    && $pubVersion['curator']
                ) {
                    $curator = User::getInstance(
                        $pubVersion['curator']
                    )->get('username');
                }
            }
        }

        // Access control
        if (!isset($dd['publication_state'])
            || $dd['publication_state'] != 1
        ) {
            // Project owners
            $sql = "SELECT username FROM #__project_owners po "
                . "JOIN #__users u ON (u.id = po.userid) "
                . "WHERE projectid = " . $db->quote($dd['project']);
            $db->setQuery($sql);
            $dd['acl']['allowed_users'] = $db->loadColumn();

            // Curators
            if (isset($dd['publication_state'])) {
                $dd['acl']['allowed_groups'] = $curatorGroups;

                if (isset($dd['acl']['allowed_users'])
                    && is_array($dd['acl']['allowed_users'])
                ) {
                    $dd['acl']['allowed_users'][] = $curator;
                }
            }
        } elseif ($dd['publication_state'] == 1) {
            $dd['acl']['allowed_users'] = false;
            $dd['acl']['allowed_groups'] = false;
            $dd['acl']['public'] = true;
        }

        $config['db']['database'] = $dd['database'];

        $dd['db_id'] = $dbId;
        $dd['dv_id'] = $dvId;

        self::ddPost($dd);

        // Dynamically set processing mode
        $driver = DataQuery::createDriver($config['db']);
        $query = new DataQuery($driver);
        $hasThreshold = !empty($config['proc_switch_threshold']);
        $cellCountThreshold = $hasThreshold
            ? $config['proc_switch_threshold']
            : 20000;
        $driver->setQuery($query->buildCountQuery($dd));
        $driver->loadAssoc();
        $driver->setQuery('SELECT FOUND_ROWS() AS total');
        $total = $driver->loadAssoc();
        $total = $total['total'] ?? 0;
        $dd['total_records'] = $total;

        $visColCount = count(array_filter(
            $dd['cols'],
            function ($col) {
                return !isset($col['hide']);
            }
        ));

        if ($cellCountThreshold < ($total * $visColCount)) {
            $dd['serverside'] = true;
        }

        return $dd;
    }

    /**
     * Set breadcrumb pathway.
     *
     * @param   array  $dd  Data definition array
     * @return  void
     */
    public function setPathway(array $dd): void
    {
        $dbId = $dd['db_id'];

        \Hubzero\Facades\Document::setTitle($dd['title']);

        if (isset($dbId['extra']) && $dbId['extra'] == 'table') {
            \Hubzero\Facades\Pathway::append(
                'Datastore',
                '/datastores/' . $dbId['name'] . '#tables'
            );
        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $refTitle = Request::getString(
                'ref_title',
                $dd['title'] . " Resource"
            );
            $refTitle = htmlentities($refTitle);
            \Hubzero\Facades\Pathway::append(
                $refTitle,
                $_SERVER['HTTP_REFERER']
            );
        }

        \Hubzero\Facades\Pathway::append($dd['title'], $_SERVER['REQUEST_URI']);
    }

    // ---------------------------------------------------------------
    // Static BC shims — used by the legacy Controller::dispatch() path
    // and com_publications cross-component calls
    // ---------------------------------------------------------------

    /**
     * @deprecated Use instance method getConfig() instead
     */
    public static function getConf($db_id)
    {
        $mode = new static();
        $mode->getConfig($db_id, DvConfig::$dv_conf);
        return DvConfig::$dv_conf;
    }

    /**
     * @deprecated Use instance method getDataDefinition() instead
     */
    public static function getDd($db_id, $dv_id = false, $version = false)
    {
        $mode = new static();
        return $mode->getDataDefinition(
            $db_id,
            DvConfig::$dv_conf,
            $dv_id ?: null,
            $version ?: null
        );
    }

    /**
     * @deprecated Use instance method setPathway() instead
     */
    public static function pathway($dd)
    {
        $mode = new static();
        $mode->setPathway($dd);
    }

    /**
     * Apply post-processing to a data definition.
     *
     * @param   array  $dd  Data definition
     * @return  array
     */
    public static function ddPost($dd)
    {
        return ModeDb::ddPost($dd);
    }
}
