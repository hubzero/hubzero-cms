<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
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
		static $dbo;

		if (!isset($dbo))
		{
			$dbo = $this->getMockBuilder('Hubzero\Database\Driver\Pdo')
			            ->disableOriginalConstructor()
			            ->setMethods(null)
			            ->getMock();

			$this->getConnection();

			$dbo->setConnection(self::$pdo)
			    ->setPrefix('');
		}

		return $dbo;
	}

	/**
	 * Resets the database handle to ensure it doesn't cause
	 * problems for subsequent tests
	 *
	 * @return  void
	 */
	public static function tearDownAfterClass()
	{
		self::$pdo = null;
	}
}
