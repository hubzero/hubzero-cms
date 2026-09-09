<?php

/**
 * Public API for cross-component access to Dataviewer functionality.
 *
 * Provides clean entry points for other components (e.g. com_publications)
 * to use Dataviewer's data export capabilities without depending on
 * internal implementation details.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Helpers;

use Components\Dataviewer\Site\Modes\ModeDsl;

class DataviewerApi
{
    /**
     * Export a project dataset as CSV.
     *
     * Used by com_publications to generate CSV exports for data attachments.
     *
     * @param   string  $dbName   Database name
     * @param   int     $version  Database version
     * @return  string|null  CSV content, or null on failure
     */
    public static function exportCsv(
        string $dbName,
        int $version
    ): ?string {
        mb_internal_encoding('UTF-8');

        $config = ConfigFactory::build();
        $dbId = ['id' => '', 'name' => '', 'mode' => 'dsl', 'extra' => false];

        $mode = new ModeDsl();
        $mode->getConfig($dbId, $config);

        $dd = $mode->getDataDefinition($dbId, $config, $dbName, $version);
        if (!$dd) {
            return null;
        }
        $dd['serverside'] = false;

        $driver = DataQuery::createDriver($config['db']);
        $limit = $config['settings']['limit'] ?? 10;
        $query = new DataQuery($driver, $limit);
        $sql = $query->buildQuery($dd);
        $result = $query->execute($sql, $dd);

        ob_start();
        \Components\Dataviewer\Site\Filter\Csv::filter($result, $dd, true);
        $csv = ob_get_contents();
        ob_end_clean();

        return $csv ?: null;
    }
}
