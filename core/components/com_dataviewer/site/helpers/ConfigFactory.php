<?php

/**
 * Configuration factory for the Dataviewer component.
 *
 * Builds the base config array from component parameters,
 * replacing the static DvConfig::init() method.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Helpers;

use Hubzero\Facades\Component;

class ConfigFactory
{
    /**
     * Build the base Dataviewer configuration from component params.
     *
     * @return  array
     */
    public static function build(): array
    {
        $comPath = str_replace(PATH_ROOT, '', dirname(__DIR__));

        $config = [];
        $config['com_name'] = 'dataviewer';
        $config['html_path'] = $comPath . '/assets';

        $params = Component::params('com_dataviewer');

        $rowOptions = [5, 10, 25, 50, 100];
        $config['settings'] = [
            'com_name'  => $config['com_name'],
            'num_rows'  => [
                'labels' => $rowOptions,
                'values' => $rowOptions,
            ],
            'limit'      => ($params->get('record_display_limit', '') === '')
                ? 10
                : (int) $params->get('record_display_limit'),
            'serverside' => false,
        ];

        // Processing mode switching
        $config['proc_mode_switch'] = $params->get('processing_mode_switch') == '1';
        $config['proc_switch_threshold'] = (int) $params->get('proc_switch_threshold');

        $config['null_desc'] = $params->get('null_desc');
        $config['help_file_base_path'] = '';
        $config['db'] = [];

        // Access Control
        $aclUsers = $params->get('acl_users');
        if ($aclUsers == 'registered') {
            $config['acl']['allowed_users'] = 'registered';
        } elseif ($aclUsers != 'registered' && $aclUsers != '') {
            $config['acl']['allowed_users'] = array_map('trim', explode(',', $aclUsers));
        } else {
            $config['acl']['allowed_users'] = false;
        }

        $aclGroups = $params->get('acl_groups');
        if ($aclGroups != '') {
            $config['acl']['allowed_groups'] = array_map('trim', explode(',', $aclGroups));
        } else {
            $config['acl']['allowed_groups'] = false;
        }

        return $config;
    }

    /**
     * Parse the db request parameter into a structured array.
     *
     * @param   string  $dbParam  Raw db parameter (e.g. "mydb:ds:table")
     * @return  array   Keys: id, name, mode, extra
     */
    public static function parseDbParam(string $dbParam): array
    {
        $dbId = [];
        $dbId['id'] = $dbParam;
        $parts = explode(':', $dbParam);
        $dbId['name'] = $parts[0];
        $dbId['mode'] = $parts[1] ?? 'db';
        $dbId['extra'] = $parts[2] ?? false;

        return $dbId;
    }

    /**
     * Resolve and instantiate the appropriate mode class.
     *
     * @param   array  $dbId  Database identifier array
     * @return  ModeInterface
     * @throws  \InvalidArgumentException  If mode class not found
     */
    public static function resolveMode(array $dbId): ModeInterface
    {
        $className = '\\Components\\Dataviewer\\Site\\Modes\\Mode'
            . ucfirst(strtolower($dbId['mode']));

        if (!class_exists($className)) {
            throw new \InvalidArgumentException(
                'Unknown dataviewer mode: ' . $dbId['mode']
            );
        }

        return new $className();
    }
}
