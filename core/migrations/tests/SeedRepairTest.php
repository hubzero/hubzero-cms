<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations\Tests;

use Hubzero\Database\Drivers\Sqlite\SqliteDriver;
use Hubzero\Test\Basic;
use Migrations\Data\UsageMetricsSeed;
use Migrations\Migration20261110130000ComUsage;
use Migrations\Migration20261110130001ComStorefront;
use PDO;

/**
 * The repairs for the seed data the converted 2.5 migrations got wrong, run
 * against SQLite databases holding what those migrations left
 */
class SeedRepairTest extends Basic
{
    /**
     * Metrics tables, with the keys the usage migrations give them
     *
     * @var  array
     */
    private const METRICS_TABLES = [
        'countries'         => 'code TEXT PRIMARY KEY, name TEXT',
        'domainclass'       => 'domain TEXT PRIMARY KEY, class INTEGER, country TEXT, state TEXT, name TEXT',
        'domainclasses'     => 'class INTEGER PRIMARY KEY, name TEXT, UNIQUE (class, name)',
        'summary_user'      => 'id INTEGER, label TEXT UNIQUE, plot INTEGER',
        'summary_andmore'   => 'id INTEGER, label TEXT UNIQUE, plot INTEGER',
        'summary_misc'      => 'id INTEGER, label TEXT UNIQUE, plot INTEGER',
        'summary_simusage'  => 'id INTEGER, label TEXT UNIQUE, plot INTEGER',
        'continents'        => 'continentSHORT TEXT, continentLONG TEXT, UNIQUE (continentSHORT, continentLONG)',
        'country_continent' => 'country TEXT, continent TEXT, UNIQUE (country, continent)',
        'exclude_list'      => 'filter TEXT, type TEXT, notes TEXT, UNIQUE (filter, type)',
    ];

    /**
     * What the broken migrations wrote that 2.4 did not: table, key, column, value
     *
     * @var  array
     */
    private const WRONG = [
        ['countries', ['code' => 'VA'], 'name', 'HOLY SEE (VATICAN CITY STATE'],
        ['countries', ['code' => 'CC'], 'name', 'COCOS (KEELING'],
        ['countries', ['code' => 'FK'], 'name', 'FALKLAND ISLANDS (MALVINAS'],
        ['domainclasses', ['class' => 1], 'name', 'EducationalInstitution'],
        ['domainclasses', ['class' => 4], 'name', 'Internet ServiceProvider'],
        ['summary_user', ['id' => 4], 'label', '- DownloadUsers {7}'],
        ['summary_misc', ['id' => 8], 'label', 'Web ServerHits'],
        ['exclude_list', ['filter' => '137.187.22.', 'type' => 'ip'], 'notes', 'NIHCrawler'],
        ['exclude_list', ['filter' => '128.231.88.', 'type' => 'ip'], 'notes', 'NIHCrawler'],
        ['exclude_list', ['filter' => '128.210.7.18', 'type' => 'ip'], 'notes', 'Purdue GoogleAppliance'],
        ['exclude_list', ['filter' => '128.46.16.59', 'type' => 'ip'], 'notes', 'Pascal Meunier\\\'s securitycrawler'],
        ['exclude_list', ['filter' => 'task=diskusage', 'type' => 'url'], 'notes', 'MiddlewareDisk Quota Checker'],
    ];

    /**
     * Tables the broken 2015 migration seeded one row at a time, keeping only the last
     *
     * @var  array
     */
    private const LAST_ROW_ONLY = [
        'domainclass',
        'summary_user',
        'summary_andmore',
        'summary_misc',
        'summary_simusage',
        'continents',
    ];

    /**
     * The 2015 migration's damage is repaired, and a second run changes nothing
     *
     * @return  void
     */
    public function testRepairsWhatTheBrokenUsageMigrationLeft()
    {
        $db = $this->metricsDatabase();

        foreach (UsageMetricsSeed::tables() as $table) {
            $rows = UsageMetricsSeed::rows($table);
            if (in_array($table, self::LAST_ROW_ONLY)) {
                $rows = [end($rows)];
            }
            // As a hub holds them: written through the query builder, which trims strings
            $rows = array_map(fn($row) => array_map(fn($v) => is_string($v) ? trim($v) : $v, $row), $rows);
            $this->insert($db, $table, $rows);
        }
        foreach (self::WRONG as [$table, $key, $column, $value]) {
            $this->setValue($db, $table, $key, $column, $value);
        }

        $this->repairUsage($db);
        $this->assertSeeded($db);

        $this->repairUsage($db);
        $this->assertSeeded($db);
    }

    /**
     * Empty tables, as the broken 2013 migration left them, are seeded
     *
     * @return  void
     */
    public function testSeedsEmptyTables()
    {
        $db = $this->metricsDatabase();

        $this->repairUsage($db);
        $this->assertSeeded($db);
    }

    /**
     * A table holding anything else is the hub's own and is left alone
     *
     * @return  void
     */
    public function testLeavesOtherContentsAlone()
    {
        $db = $this->metricsDatabase();

        $mine = [
            ['domain' => 'example.edu', 'class' => 1, 'country' => 'us', 'state' => '', 'name' => 'Example'],
            ['domain' => 'example.com', 'class' => 2, 'country' => 'us', 'state' => '', 'name' => 'Example Inc'],
        ];
        $this->insert($db, 'domainclass', $mine);

        $kept = ['code' => 'VA', 'name' => 'Vatican'];
        $this->insert($db, 'countries', [$kept]);

        $this->repairUsage($db);

        $this->assertEquals($mine, $this->rows($db, 'domainclass'));
        $this->assertEquals([$kept], $this->rows($db, 'countries'));
    }

    /**
     * The misspelled product type is renamed, and only when it is there alone
     *
     * @return  void
     */
    public function testRenamesTheMisspelledProductType()
    {
        foreach (
            [
                [['SoftwareDownload'], ['Software Download']],
                [['SoftwareDownload', 'Software Download'], ['Software Download', 'SoftwareDownload']],
                [['Software Download'], ['Software Download']],
                [[], []],
            ] as [$before, $after]
        ) {
            $db = $this->database();
            $db->getConnection()->exec(
                'CREATE TABLE storefront_product_types (ptId INTEGER PRIMARY KEY, ptName TEXT, ptModel TEXT)',
            );
            foreach ($before as $name) {
                $this->insert($db, 'storefront_product_types', [['ptName' => $name, 'ptModel' => 'software']]);
            }

            $migration = new Migration20261110130001ComStorefront($db);
            $migration->up();

            $names = array_column($this->rows($db, 'storefront_product_types'), 'ptName');
            sort($names);
            $this->assertEquals($after, $names);
        }
    }

    /**
     * Run the usage repair against a database
     *
     * @param   SqliteDriver  $db
     * @return  void
     */
    private function repairUsage($db)
    {
        $migration = new class ($db) extends Migration20261110130000ComUsage {
            protected function metricsDatabase()
            {
                return $this->db;
            }
        };
        $migration->up();
    }

    /**
     * Every metrics table holds exactly 2.4's rows, as the query builder stores
     * them: it trims the strings it binds
     *
     * @param   SqliteDriver  $db
     * @return  void
     */
    private function assertSeeded($db)
    {
        foreach (UsageMetricsSeed::tables() as $table) {
            $want = array_map(fn($row) => array_map(fn($v) => trim((string) $v), $row), UsageMetricsSeed::rows($table));
            $have = array_map(fn($row) => array_map('strval', $row), $this->rows($db, $table));
            sort($want);
            sort($have);
            $this->assertSame($want, $have, $table);
        }
    }

    /**
     * An in-memory database with the metrics tables
     *
     * @return  SqliteDriver
     */
    private function metricsDatabase()
    {
        $db = $this->database();
        foreach (self::METRICS_TABLES as $table => $columns) {
            $db->getConnection()->exec("CREATE TABLE {$table} ({$columns})");
        }
        return $db;
    }

    /**
     * An empty in-memory database
     *
     * @return  SqliteDriver
     */
    private function database()
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $driver = $this
            ->getMockBuilder(SqliteDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $driver->setConnection($pdo)->setPrefix('');

        return $driver;
    }

    /**
     * @param   SqliteDriver  $db
     * @param   string        $table
     * @param   array         $rows   Rows keyed by column
     * @return  void
     */
    private function insert($db, $table, array $rows)
    {
        foreach ($rows as $row) {
            $statement = $db->getConnection()->prepare(
                "INSERT INTO {$table} (" . implode(', ', array_keys($row)) . ') VALUES ('
                    . implode(', ', array_fill(0, count($row), '?')) . ')',
            );
            $statement->execute(array_values($row));
        }
    }

    /**
     * @param   SqliteDriver  $db
     * @param   string        $table
     * @param   array         $key
     * @param   string        $column
     * @param   string        $value
     * @return  void
     */
    private function setValue($db, $table, array $key, $column, $value)
    {
        $where = implode(' AND ', array_map(fn($field) => "{$field} = ?", array_keys($key)));
        $db->getConnection()
            ->prepare("UPDATE {$table} SET {$column} = ? WHERE {$where}")
            ->execute(array_merge([$value], array_values($key)));
    }

    /**
     * @param   SqliteDriver  $db
     * @param   string        $table
     * @return  array
     */
    private function rows($db, $table)
    {
        return $db->getConnection()->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
    }
}
