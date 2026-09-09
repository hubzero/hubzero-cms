<?php

/**
 * DataStore mode for the Dataviewer component.
 *
 * Resolves database connections and data definitions from
 * com_datastores configuration.
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

class ModeDs implements ModeInterface
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
        $params = \Hubzero\Facades\Component::params('com_datastores');
        $config['db']['host'] = $params->get('db_host');
        $config['db']['user'] = $params->get('db_ro_user');
        $config['db']['password'] = $params->get('db_ro_pass');
        $config['db']['database'] = 'ds_' . $dbId['name'];

        // DataStores base directory
        $dsBaseDir = $params->get('base_dir');
        if ($dsBaseDir == '') {
            $dsBaseDir = '/data/datastores';
        }
        $config['base_path'] = $dsBaseDir . '/' . $dbId['name'];

        return $config;
    }

    /**
     * Get the data definition for the requested dataview.
     *
     * @param   array  $dbId    Database identifier array
     * @param   array  $config  Config array (may be modified)
     * @return  array|null  Data definition array, or null if not found
     */
    public function getDataDefinition(array $dbId, array &$config): ?array
    {
        $dd = null;
        $db = \Hubzero\Facades\App::get('db');
        $dvId = Request::getVar('dv');

        if ($dbId['extra']) {
            $sql = "SELECT * FROM `#__datastore_tables` WHERE datastore_id = "
                . $dbId['name'] . " AND id = " . $db->quote($dvId);
            $db->setQuery($sql);
            $r = $db->loadAssoc();

            $td = json_decode($r['table_definition'], true);

            $dd = [];
            $dd['db'] = $config['db'];
            $dd['db']['name'] = 'ds_' . $r['datastore_id'];
            $dd['table'] = $td['name'];
            $dd['title'] = $r['name'];

            if ($dbId['extra'] == 'table' || $dbId['extra'] == 'update') {
                if ($dbId['extra'] == 'update') {
                    $updateLink = '/datastores/' . $dbId['name']
                        . '/table/data_record_update/?table=' . $dvId
                        . '&__ds_rec_id=';

                    $dd['cols'][$td['name'] . '.__ds_rec_id'] = [
                        'label'      => 'Select <br />Record',
                        'raw'        => "CONCAT('$updateLink', __ds_rec_id)",
                        'type'       => 'link',
                        'relative'   => 'true',
                        'link_label' => 'Edit',
                        'link_title' => 'Click here to update or remove this record',
                        'popup'      => [
                            'window'   => 'Edit_Record',
                            'features' => 'width=1175px,resizable,scrollbars,status',
                        ],
                    ];
                }

                foreach ($td['columns'] as $col) {
                    if ($col['name'] != '__ds_rec_id') {
                        $colKey = $td['name'] . '.' . $col['name'];
                        if ($col['type'] == 'file') {
                            $dd['cols'][$colKey]['type'] = 'file';
                            $dd['cols'][$colKey]['type_extra'] = $col['type_extra'];
                            $dd['cols'][$colKey]['ds-repo-path'] = "/file_repo/{$td['name']}/{$col['name']}";
                            $dd['cols'][$colKey]['file-verify'] = true;
                        }
                        if ($col['type'] == 'url') {
                            $dd['cols'][$colKey]['type'] = 'url';
                            $dd['cols'][$colKey]['url-display'] = 'full_link';
                        }
                        $isLargeText = $col['type'] == 'txt'
                            && ($col['type_extra'] == 'medium'
                                || $col['type_extra'] == 'large');
                        if ($isLargeText) {
                            $dd['cols'][$colKey]['width'] = '150';
                            $dd['cols'][$colKey]['truncate'] = 'truncate';
                        }
                        $dd['cols'][$colKey]['label'] = $col['label'];
                    }
                }
            }
        } else {
            $path = $config['base_path'] . "/datadefinitions";
            $ddFile = "$dvId.json";
            if (file_exists("$path/$ddFile")) {
                $dd = json_decode(file_get_contents("$path/$ddFile"), true);
            } else {
                return null;
            }
        }

        $dd['db_id'] = $dbId;
        $dd['dv_id'] = $dvId;

        $dd = self::ddPost($dd);

        $dd['conf'] = $dd['conf'] ?? [];

        if (isset($dd['conf']['proc_mode_switch'])) {
            $config['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
        }
        if (isset($dd['conf']['proc_switch_threshold'])) {
            $config['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
        }

        // Dynamically set processing mode
        if (!empty($config['proc_mode_switch'])) {
            $driver = DataQuery::createDriver($config['db']);
            $query = new DataQuery($driver);
            $driver->setQuery($query->buildCountQuery($dd));
            $driver->loadAssoc();
            $driver->setQuery('SELECT FOUND_ROWS() AS total');
            $total = $driver->loadAssoc();
            if ($total) {
                $total = $total['total'] ?? 0;
                $dd['total_records'] = $total;

                $visColCount = 0;
                if (isset($dd['cols'])) {
                    $visColCount = count(array_filter(
                        $dd['cols'],
                        function ($col) {
                            return !isset($col['hide']);
                        }
                    ));
                }

                if ($config['proc_switch_threshold'] < ($total * $visColCount)) {
                    $dd['serverside'] = true;
                }
            }
        }

        // Record Filters
        if (isset($dd['record_filters']) && is_array($dd['record_filters'])) {
            foreach ($dd['record_filters'] as $f) {
                $clause = $this->buildRecordFilterClause($f, $db);
                if ($clause) {
                    $dd['where'][] = ['raw' => $clause];
                }
            }
        }

        // ACL — check resource association
        $sql = "SELECT r.id, r.published, r.access, r.group_owner, "
            . "r.group_access, dv.path "
            . "FROM `#__datastore_resources` AS dr "
            . "LEFT JOIN (`#__resources` AS r, `#__resource_assoc` ra, "
            . "`#__resources` AS dv) "
            . "ON (r.id = dr.resource_id AND ra.parent_id = r.id "
            . "AND ra.child_id = dv.id) "
            . "WHERE r.id IS NOT NULL AND r.published = 1 "
            . "AND dr.datastore_id = " . $db->quote($dbId['name'])
            . " AND dv.path = " . $db->quote(
                "/dataviewer/view/{$dbId['name']}:ds/$dvId/"
            );
        $db->setQuery($sql);
        $res = $db->loadAssoc();

        if (isset($res['id'])) {
            $dd['acl'] = [];
            if ($res['access'] == 0) {
                $dd['acl']['public'] = true;
            }
        }

        // DataStore managers
        $sql = "SELECT username FROM `#__datastore_users` ds "
            . "LEFT JOIN `#__users` u ON (u.id = ds.value AND ds.type='user') "
            . "WHERE ds.id = " . $db->quote($dbId['name']);
        $db->setQuery($sql);
        $managers = $db->loadColumn();

        if (!isset($dd['acl'])) {
            $dd['acl']['allowed_users'] = $managers;
        } elseif (!isset($dd['acl']['registered'])
            && !isset($dd['acl']['public'])
        ) {
            $dd['acl']['allowed_users'] = $dd['acl']['allowed_users'] ?? [];
            $dd['acl']['allowed_users'] = array_merge(
                $dd['acl']['allowed_users'],
                $managers
            );
        }

        // Hub admins get full access
        if (\Hubzero\Access\Access::check(User::get('id'), 'core.admin')) {
            $dd['acl']['allowed_users'] = $dd['acl']['allowed_users'] ?? [];
            $dd['acl']['allowed_users'][] = User::get('username');
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

        $document = \Hubzero\Facades\App::get('document');
        $document->setTitle($dd['title']);

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

    /**
     * Build a WHERE clause from a record filter definition.
     *
     * @param   array   $f   Filter definition with 'type', 'col', 'val'
     * @param   object  $db  Database driver for quoting
     * @return  string|null
     */
    protected function buildRecordFilterClause(array $f, $db): ?string
    {
        $col = $f['col'];
        $val = $db->quote($f['val']);

        switch ($f['type']) {
            case 'E':
                return "$col = $val";
            case 'NE':
                return "$col <> $val";
            case 'LT':
                return "$col < $val";
            case 'GT':
                return "$col > $val";
            case 'LK':
                return "$col LIKE " . $db->quote('%' . $f['val'] . '%');
            case 'NLK':
                return "$col NOT LIKE " . $db->quote('%' . $f['val'] . '%');
            case 'NULL':
                return "$col IS NULL";
            case 'NNULL':
                return "$col IS NOT NULL";
        }

        return null;
    }

    // ---------------------------------------------------------------
    // Static BC shims — used by the legacy Controller::dispatch() path
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
    public static function getDd($db_id)
    {
        $mode = new static();
        return $mode->getDataDefinition($db_id, DvConfig::$dv_conf);
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
