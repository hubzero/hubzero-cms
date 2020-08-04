<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests;

$componentPath = Component::path('com_courses');

require_once "$componentPath/helpers/queryAddColumnStatement.php";

use Hubzero\Test\Basic;
use Components\Courses\Helpers\QueryAddColumnStatement;

class QueryAddColumnStatementTest extends Basic
{
	/**
	 * Test toString returns correct string when name and type provided
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenNameAndTypeProvided()
	{
		$columnData = [
			'name' => 'test',
			'type' => 'varchar(255)'
		];
		$addColumnStatement = new QueryAddColumnStatement($columnData);
		$expectedStatement = 'ADD COLUMN test varchar(255)';

		$actualStatement = $addColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}

	/**
	 * Test toString returns correct string when restriction provided
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenRestrictionProvided()
	{
		$columnData = [
			'name' => 'test',
			'type' => 'varchar(255)',
			'restriction' => 'NOT NULL'
		];
		$addColumnStatement = new QueryAddColumnStatement($columnData);
		$expectedStatement = 'ADD COLUMN test varchar(255) NOT NULL';

		$actualStatement = $addColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}

	/**
	 * Test toString returns correct string when default provided
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenDefaultProvided()
	{
		$columnData = [
			'name' => 'test',
			'type' => 'varchar(255)',
			'default' => "'foo'"
		];
		$addColumnStatement = new QueryAddColumnStatement($columnData);
		$expectedStatement = "ADD COLUMN test varchar(255) DEFAULT 'foo'";

		$actualStatement = $addColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}

	/**
	 * Test toString returns correct string when restriction and default provided
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenRestrictionAndDefaultProvided()
	{
		$columnData = [
			'name' => 'test',
			'type' => 'varchar(255)',
			'restriction' => 'NOT NULL',
			'default' => "'foo'"
		];
		$addColumnStatement = new QueryAddColumnStatement($columnData);
		$expectedStatement = "ADD COLUMN test varchar(255) NOT NULL DEFAULT 'foo'";

		$actualStatement = $addColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}

	/**
	 * Test toString returns correct string when restriction and default 0
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenRestrictionAndDefaultZero()
	{
		$columnData = [
			'name' => 'test',
			'type' => 'varchar(255)',
			'restriction' => 'NOT NULL',
			'default' => 0
		];
		$addColumnStatement = new QueryAddColumnStatement($columnData);
		$expectedStatement = "ADD COLUMN test varchar(255) NOT NULL DEFAULT 0";

		$actualStatement = $addColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}
}
