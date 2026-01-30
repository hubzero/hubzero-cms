<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

use Hubzero\Test\Database\Connection;
use Hubzero\Test\Database\DataSet;
use Hubzero\Test\Database\Table;
use Hubzero\Test\Database\XmlDataSet;
use Hubzero\Test\Database\FlatXmlDataSet;
use Hubzero\Container\Container;
use Hubzero\Events\Dispatcher;
use Hubzero\Facades\Facade;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit test that requires database features
 *
 * This is a standalone replacement for PHPUnit\DbUnit\TestCase that provides
 * similar functionality without requiring the deprecated DBUnit package.
 */
class Database extends TestCase
{
    /**
     * The application container (shared across tests)
     *
     * @var Container|null
     */
    protected static $app = null;

    /**
     * The database PDO object (shared across tests in a class)
     *
     * @var \PDO|null
     */
    protected static $pdo = null;

    /**
     * The database driver mock
     *
     * @var object|null
     */
    protected static $dbo = null;

    /**
     * The database connection wrapper
     *
     * @var Connection|null
     */
    protected $connection = null;

    /**
     * Whether the database has been seeded for this test
     *
     * @var bool
     */
    protected static $seeded = false;

    /**
     * The database fixture name
     *
     * We assume that you'll have a test sqlite database with your test schema defined.
     * If you don't need this, then why aren't you using the basic test class?
     *
     * @var string
     */
    protected $fixture = 'test.sqlite3';

    /**
     * The database seed data
     *
     * This var can be overwritten if you only have one seed file. If you have multiple files,
     * or need to do something a little more complicated, you can also completely override the
     * getDataSet() method.
     *
     * @var string
     */
    protected $seed = 'seed.xml';

    /**
     * Set up facades before any tests run
     *
     * This bootstraps the Event facade and other facades needed for testing
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Only bootstrap facades once
        if (self::$app === null) {
            self::$app = new Container();

            // Register the event dispatcher
            self::$app['dispatcher'] = function () {
                return new Dispatcher();
            };

            // Set up the facade application
            Facade::setApplication(self::$app);

            // Create facade aliases so Event:: can be used globally
            Facade::createAliases([
                'Event' => \Hubzero\Facades\Event::class,
            ]);
        }
    }

    /**
     * Set up runs before each test
     *
     * Seeds the database with test data
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure connection is established
        $this->getConnection();

        // Seed the database before each test
        $this->seedDatabase();
    }

    /**
     * Gets the default database connection
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        // Only get the connection once per test
        if ($this->connection === null) {
            // Only create the PDO object once per file
            if (self::$pdo === null) {
                $filename = (new \ReflectionClass(get_called_class()))->getFileName();
                $dbPath = dirname($filename) . DS . 'Fixtures' . DS . $this->fixture;

                // Create database and schema if it doesn't exist
                $needsSchema = !file_exists($dbPath);

                self::$pdo = new \PDO('sqlite:' . $dbPath);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                if ($needsSchema) {
                    $this->createSchemaFromSeed();
                }
            }

            $this->connection = new Connection(self::$pdo, 'main');
        }

        return $this->connection;
    }

    /**
     * Creates database schema by parsing the seed XML file
     *
     * @return void
     */
    protected function createSchemaFromSeed(): void
    {
        $filename = (new \ReflectionClass(get_called_class()))->getFileName();
        $seedPath = dirname($filename) . DS . 'Fixtures' . DS . $this->seed;

        if (!file_exists($seedPath)) {
            return;
        }

        $xml = simplexml_load_file($seedPath);
        if ($xml === false) {
            return;
        }

        foreach ($xml->table as $table) {
            $tableName = (string) $table['name'];
            $columns = [];

            foreach ($table->column as $column) {
                $columnName = (string) $column;
                // Use TEXT for all columns - SQLite is type-flexible
                // Use INTEGER PRIMARY KEY for 'id' columns for auto-increment
                if ($columnName === 'id' || $columnName === 'extension_id') {
                    $columns[] = '"' . $columnName . '" INTEGER PRIMARY KEY';
                } else {
                    $columns[] = '"' . $columnName . '" TEXT';
                }
            }

            if (!empty($columns)) {
                $sql = 'CREATE TABLE IF NOT EXISTS "' . $tableName . '" (' . implode(', ', $columns) . ')';
                self::$pdo->exec($sql);
            }
        }
    }

    /**
     * Gets the database seed info
     *
     * @return DataSet
     */
    public function getDataSet(): DataSet
    {
        $filename = (new \ReflectionClass(get_called_class()))->getFileName();

        return $this->createXMLDataSet(dirname($filename) . DS . 'Fixtures' . DS . $this->seed);
    }

    /**
     * Seeds the database with the test data
     *
     * @return void
     */
    protected function seedDatabase(): void
    {
        $dataset = $this->getDataSet();
        $connection = $this->getConnection();

        foreach ($dataset->getTables() as $table) {
            $tableName = $table->getTableName();

            // Clear the table first
            $connection->truncate($tableName);

            // Insert all rows
            foreach ($table->getRows() as $row) {
                $connection->insert($tableName, $row);
            }
        }
    }

    /**
     * Gets a mock database driver
     *
     * Uses the SQLite driver since tests use SQLite databases,
     * which ensures proper SQL-standard quoting (double quotes for identifiers).
     *
     * @return object
     */
    public function getMockDriver()
    {
        if (!isset(self::$dbo)) {
            self::$dbo = $this->getMockBuilder('Hubzero\Database\Driver\Sqlite')
                        ->disableOriginalConstructor()
                        ->onlyMethods([])
                        ->getMock();

            $this->getConnection();

            self::$dbo->setConnection(self::$pdo)
                ->setPrefix('');
        }

        return self::$dbo;
    }

    /**
     * Create a DataSet from standard XML format
     *
     * @param  string  $xmlFile  Path to the XML file
     * @return DataSet
     */
    protected function createXMLDataSet(string $xmlFile): DataSet
    {
        return XmlDataSet::load($xmlFile);
    }

    /**
     * Create a DataSet from flat XML format
     *
     * @param  string  $xmlFile  Path to the XML file
     * @return DataSet
     */
    protected function createFlatXmlDataSet(string $xmlFile): DataSet
    {
        return FlatXmlDataSet::load($xmlFile);
    }

    /**
     * Assert that two tables are equal
     *
     * @param  Table   $expected
     * @param  Table   $actual
     * @param  string  $message
     * @return void
     */
    public function assertTablesEqual(Table $expected, Table $actual, string $message = ''): void
    {
        if (!$expected->matches($actual)) {
            $diff = $expected->getDifference($actual);
            $failMessage = $message ? $message . "\n" . $diff : $diff;
            $this->fail($failMessage);
        }

        // If we get here, tables match
        $this->assertTrue(true);
    }

    /**
     * Resets the database handle to ensure it doesn't cause
     * problems for subsequent tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        self::$dbo = null;
        self::$pdo = null;
        self::$seeded = false;
        // Note: We don't reset self::$app or Facade here because
        // other test classes may still need the facades
    }
}
