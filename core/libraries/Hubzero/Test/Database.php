<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

			$dbo->setConnection(self::$pdo)
			    ->setPrefix('');
		}

		return $dbo;
	}
}