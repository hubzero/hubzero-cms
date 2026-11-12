<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Facades\Config;
use Migrations\Data\UsageMetricsSeed;

/**
 * Migration script for repairing the metrics seed data the converted usage
 * migrations wrote
 *
 * Migration20150904235936ComUsage, as converted to the query builder in 2.5,
 * kept only the last row of each table it seeded one row at a time, and
 * misspelled some names in the tables it seeded whole. Migration20131112130740ComUsage
 * built its rows and never inserted them, leaving tables empty. Both are fixed;
 * this repairs the metrics databases they already ran against.
 *
 * Nothing is changed unless the damage is found: a value is corrected only
 * where it still holds exactly what the broken migration wrote, and a table
 * is filled only when it is empty or holds nothing but the one row the broken
 * loop left. A table with any other contents is the hub's and is left alone.
 **/
class Migration20261110130000ComUsage extends Base
{
    /**
     * Values the broken migration wrote: table, key, column, what it wrote,
     * and what 2.4 wrote
     *
     * @var  array
     */
    private const DAMAGED = [
        ['countries', ['code' => 'VA'], 'name', 'HOLY SEE (VATICAN CITY STATE', 'HOLY SEE (VATICAN CITY STATE)'],
        ['countries', ['code' => 'CC'], 'name', 'COCOS (KEELING', 'COCOS (KEELING) ISLANDS'],
        ['countries', ['code' => 'FK'], 'name', 'FALKLAND ISLANDS (MALVINAS', 'FALKLAND ISLANDS (MALVINAS)'],
        ['domainclasses', ['class' => '1'], 'name', 'EducationalInstitution', 'Educational Institution'],
        ['domainclasses', ['class' => '4'], 'name', 'Internet ServiceProvider', 'Internet Service Provider'],
        ['summary_user', ['id' => '4'], 'label', '- DownloadUsers {7}', ' - Download Users {7}'],
        ['summary_misc', ['id' => '8'], 'label', 'Web ServerHits', 'Web Server Hits'],
        ['exclude_list', ['filter' => '137.187.22.', 'type' => 'ip'], 'notes', 'NIHCrawler', 'NIH Crawler'],
        ['exclude_list', ['filter' => '128.231.88.', 'type' => 'ip'], 'notes', 'NIHCrawler', 'NIH Crawler'],
        [
            'exclude_list',
            ['filter' => '128.210.7.18', 'type' => 'ip'],
            'notes',
            'Purdue GoogleAppliance',
            'Purdue Google Appliance',
        ],
        [
            'exclude_list',
            ['filter' => '128.46.16.59', 'type' => 'ip'],
            'notes',
            'Pascal Meunier\\\'s securitycrawler',
            'Pascal Meunier\'s security crawler',
        ],
        [
            'exclude_list',
            ['filter' => 'task=diskusage', 'type' => 'url'],
            'notes',
            'MiddlewareDisk Quota Checker',
            'Middleware Disk Quota Checker',
        ],
    ];

    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        require_once __DIR__ . '/data/UsageMetricsSeed.php';

        $statsDb = $this->metricsDatabase();

        // Without a metrics database there is nothing to repair; the fixed
        // migrations seed it correctly once one is set up.
        if (!$statsDb) {
            return;
        }

        foreach (self::DAMAGED as [$table, $key, $column, $wrote, $should]) {
            if (!$statsDb->schema()->tableExists($table)) {
                continue;
            }

            $query = $statsDb
                ->getQuery(true)
                ->select($column)
                ->from($table)
                ->where($column, '=', $wrote);
            foreach ($key as $field => $value) {
                $query->where($field, '=', $value);
            }

            if ($query->doesntExist()) {
                continue;
            }

            $update = $statsDb
                ->getQuery(true)
                ->update($table)
                ->set([$column => $should])
                ->where($column, '=', $wrote);
            foreach ($key as $field => $value) {
                $update->where($field, '=', $value);
            }
            $update->execute();

            $this->log("Corrected {$table}.{$column} where " . json_encode($key));
        }

        foreach (UsageMetricsSeed::tables() as $table) {
            if (!$statsDb->schema()->tableExists($table) || !$this->seededBroken($statsDb, $table)) {
                continue;
            }

            $statsDb
                ->getQuery(true)
                ->insertMany($table, UsageMetricsSeed::rows($table), true);

            $this->log("Seeded the missing rows of {$table}");
        }
    }

    /**
     * Whether a table holds what the broken migrations left: nothing, or only
     * the last of its seed rows
     *
     * @param   object  $statsDb  Metrics database
     * @param   string  $table    Table name
     * @return  bool
     */
    private function seededBroken($statsDb, $table)
    {
        $rows = $statsDb
            ->getQuery(true)
            ->select('*')
            ->from($table)
            ->limit(2)
            ->loadAssocList();

        if (empty($rows)) {
            return true;
        }

        if (count($rows) > 1) {
            return false;
        }

        $seed = UsageMetricsSeed::rows($table);
        $last = end($seed);

        // The query builder trims the strings it binds, so the row was stored
        // without the leading or trailing spaces some seed values have
        foreach ($last as $column => $value) {
            if (!array_key_exists($column, $rows[0]) || trim((string) $rows[0][$column]) !== trim((string) $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The metrics database, or null when it cannot be reached
     *
     * Found the way Migration20150904235936ComUsage finds it.
     *
     * @return  object|null
     */
    protected function metricsDatabase()
    {
        $config = $this->getParams('com_usage');

        $options = [
            'driver'   => 'pdo',
            'host'     => $config->get('statsDBHost') ?: Config::get('host'),
            'port'     => $config->get('statsDBPort') ?: Config::get('port'),
            'user'     => $config->get('statsDBUsername') ?: Config::get('user'),
            'password' => $config->get('statsDBPassword') ?: Config::get('password'),
            'database' => $config->get('statsDBDatabase') ?: Config::get('db') . '_metrics',
        ];

        try {
            $statsDb = \Hubzero\Database\Driver::getInstance($options);
            $statsDb->connect();
        } catch (\Hubzero\Database\Exception\ConnectionFailedException $e) {
            return null;
        }

        return $statsDb;
    }
}
