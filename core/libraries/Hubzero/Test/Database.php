<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

/**
 * PHPUnit test that requires the extended database features
 */
class Database extends \PHPUnit_Extensions_Database_TestCase
{
	/**
	 * The database pdo object
	 *
	 * @var  object
	 */
	static protected $pdo = null;

	/**
	 * The database driver
	 *
	 * @var  object
	 */
	static protected $dbo = null;

	/**
	 * The database connection
	 *
	 * @var  object
	 */
	protected $connection = null;

	/**
	 * The database fixture name
	 *
	 * We assume that you'll have a test sqlite database with your test schema defined.
	 * If you don't need this, then why aren't you using the basic test class?
	 *
	 * @var  string
	 */
	protected $fixture = 'test.sqlite3';

	/**
	 * The database seed data
	 *
	 * This var can be overwritten if you only have one seed file.  If you have multiple files,
	 * or need to do something a little more complicated, you can also completely override the
	 * getDataSet() method.
	 *
	 * @var  string
	 */
	protected $seed = 'seed.xml';

	/**
	 * Gets the default database connection (PHPUnit uses this during setup)
	 *
	 * @return  object  PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	public function getConnection()
	{
		// Only get the connection once per test
		if (is_null($this->connection))
		{
			// Only create the pdo object once per file
			if (is_null(self::$pdo))
			{
				$filename = with(new \ReflectionClass(get_called_class()))->getFileName();

				self::$pdo = new \PDO('sqlite:' . dirname($filename) . DS . 'Fixtures' . DS . $this->fixture);
			}

			$this->connection = $this->createDefaultDBConnection(self::$pdo, 'main');
		}

		return $this->connection;
	}

	/**
	 * Gets the database seed info (PHPUnit does this for every test)
	 *
	 * @return  object  PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet()
	{
		$filename = with(new \ReflectionClass(get_called_class()))->getFileName();

		return $this->createXMLDataSet(dirname($filename) . DS . 'Fixtures' . DS . $this->seed);
	}

	/**
	 * Gets a mock database driver
	 *
	 * @return  object
	 */
	public function getMockDriver()
	{
		if (!isset(self::$dbo))
		{
			self::$dbo = $this->getMockBuilder('Hubzero\Database\Driver\Pdo')
			            ->disableOriginalConstructor()
			            ->setMethods(null)
			            ->getMock();

			$this->getConnection();

			self::$dbo->setConnection(self::$pdo)
			    ->setPrefix('');
		}

		return self::$dbo;
	}

	/**
	 * Resets the database handle to ensure it doesn't cause
	 * problems for subsequent tests
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		self::$dbo = null;
		self::$pdo = null;
	}
}
