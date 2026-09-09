<?php

/**
 * Database mode for the Dataviewer component.
 *
 * Resolves database connections and data definitions from
 * filesystem-based JSON config files (com_databases).
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

class ModeDb implements ModeInterface
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
        $dbName = $dbId['name'] ?? '';

        if (empty($dbName)) {
            return $config;
        }

        // Base directory from com_databases params
        $baseDir = \Hubzero\Facades\Component::params('com_databases')->get('base_dir');
        if (!$baseDir || $baseDir == '') {
            $baseDir = '/db/databases';
        }
        $config['db_base_dir'] = $baseDir;

        // Read database connection config
        $dbConfFile = "$baseDir/$dbName/database.json";
        $dbConf = json_decode(file_get_contents($dbConfFile), true);
        $config['db'] = array_merge($config['db'], $dbConf['database_ro']);

        // Read optional dataviewer-specific config
        $comName = $config['com_name'] ?? 'dataviewer';
        $dvConfFile = "$baseDir/$dbName/applications/$comName/config.json";

        if (file_exists($dvConfFile)) {
            $dbDvConf = json_decode(file_get_contents($dvConfFile), true);
            if (!is_array($dbDvConf)) {
                $dbDvConf = [];
            }
            if (isset($dbDvConf['settings'])) {
                $dbDvConf['settings'] = array_merge(
                    $config['settings'],
                    $dbDvConf['settings']
                );
            }
            $config = array_merge($config, $dbDvConf);
        }

        if (!isset($config['base_path'])) {
            $config['base_path'] = '';
        }

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
        $dvId = Request::getString('dv');
        $dbName = $dbId['name'];

        $ddBase = $config['db_base_dir'];
        $ddPath = "$ddBase/$dbName/applications/dataviewer/datadefinitions";
        $config['dd_json'] = $ddPath;

        $jsonFile = $ddPath . DS . $dvId . '.json';
        $phpFile = $ddPath . DS . $dvId . '.php';

        if (isset($dbId['extra']) && $dbId['extra'] == 'table') {
            $dd = [];
            $dd['title'] = 'Table : ' . $dvId;
            $dd['table'] = $dvId;

            $hasManagers = isset($config['_managers'])
                && $config['_managers'] !== false;
            if (!User::isGuest() && $hasManagers) {
                $dd['acl']['allowed_groups'] = $config['_managers'];
            } elseif (!User::isGuest()
                && User::authorise('login', 'administrator')
            ) {
                $dd['acl']['allowed_users'] = false;
                $dd['acl']['allowed_groups'] = false;
            }
        } else {
            if (file_exists($jsonFile)) {
                $dd = json_decode(file_get_contents($jsonFile), true);
            } elseif (file_exists($phpFile)) {
                require_once $phpFile;
                $ddFunc = 'get_' . $dvId;
                if (function_exists($ddFunc)) {
                    $dd = $ddFunc();
                }
            } else {
                \Hubzero\Facades\App::abort(
                    404,
                    'Invalid or Missing Dataview'
                );
                return null;
            }

            $dd['conf'] = $dd['conf'] ?? [];

            if (isset($dd['conf']['proc_mode_switch'])) {
                $config['proc_mode_switch'] = $dd['conf']['proc_mode_switch'];
            }
            if (isset($dd['conf']['proc_switch_threshold'])) {
                $config['proc_switch_threshold'] = $dd['conf']['proc_switch_threshold'];
            }

            // Database override from dd
            if (isset($dd['db']) && is_array($dd['db'])) {
                $config['db'] = array_merge($config['db'], $dd['db']);
            }

            $dd = self::ddPost($dd);
        }

        // Dynamically set processing mode
        if (!empty($config['proc_mode_switch'])) {
            $driver = DataQuery::createDriver($config['db']);
            $query = new DataQuery($driver);
            $driver->setQuery($query->buildCountQuery($dd));
            $driver->loadAssoc();
            $driver->setQuery('SELECT FOUND_ROWS() AS total');
            $total = $driver->loadAssoc();
            $total = isset($total['total']) ? $total['total'] : 0;
            $dd['total_records'] = $total;

            $visColCount = 0;
            if (isset($dd['cols'])) {
                $visColCount = count(array_filter($dd['cols'], function ($col) {
                    return !isset($col['hide']);
                }));
            } elseif (isset($dbId['extra']) && $dbId['extra'] == 'table') {
                $sql = "SELECT COUNT(*) AS cols FROM information_schema.columns"
                    . " WHERE table_name = " . $driver->quote($dd['table']);
                $driver->setQuery($sql);
                $cols = $driver->loadAssoc();
                $visColCount = $cols['cols'];
            }

            if ($config['proc_switch_threshold'] < ($total * $visColCount)) {
                $dd['serverside'] = true;
            }
        }

        $dd['db_id'] = $dbId;
        $dd['dv_id'] = $dvId;

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
        $document = \Hubzero\Facades\App::get('document');
        $document->setTitle($dd['title']);

        if (isset($_SERVER['HTTP_REFERER'])) {
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
     * Apply post-processing to a data definition (custom views, filters).
     *
     * @param   array  $dd  Data definition
     * @return  array  Modified data definition
     */
    public static function ddPost($dd)
    {
        $id = Request::getString('id', false);

        if ($id) {
            $dd['where'][] = ['field' => $dd['pk'], 'value' => $id];
            $dd['single'] = true;
        }

        $customField = Request::getString('custom_field', false);
        if ($customField) {
            $parts = explode('|', $customField);
            $dd['where'][] = ['field' => $parts[0], 'value' => $parts[1]];
            $dd['single'] = true;
        }

        // Custom Views
        $customView = Request::getString('custom_view', '');
        if ($customView != '') {
            $customView = explode(',', $customView);
            unset($dd['customizer']);

            $customTitle = Request::getString('custom_title', '');
            if ($customTitle !== '') {
                $dd['title'] = htmlspecialchars($customTitle);
            }

            $groupBy = Request::getString('group_by', '');
            if ($groupBy !== '') {
                $dd['group_by'] = htmlspecialchars($groupBy);
            }

            // Reorder columns
            $orderCols = $dd['cols'];
            $dd['cols'] = [];
            foreach ($customView as $cvCol) {
                $dd['cols'][$cvCol] = $orderCols[$cvCol];
            }

            // Hide non-selected columns
            foreach ($orderCols as $colId => $prop) {
                if (!in_array($colId, $customView)) {
                    $dd['cols'][$colId] = $prop;
                    if (!isset($dd['cols'][$colId]['hide'])) {
                        $dd['cols'][$colId]['hide'] = 'custom';
                    }
                }
            }
        }

        return $dd;
    }
}
