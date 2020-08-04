<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests;

$componentPath = Component::path('com_courses');

require_once "$componentPath/helpers/queryDropColumnStatement.php";

use Hubzero\Test\Basic;
use Components\Courses\Helpers\QueryDropColumnStatement;

class QueryDropColumnStatementTest extends Basic
{
	/**
	 * Test toString()
	 *
	 * @return  void
	 */
	public function testToStringReturnsCorrectStringWhenNameAndTypeProvided()
	{
		$columnData = ['name' => 'test'];
		$dropColumnStatement = new QueryDropColumnStatement($columnData);
		$expectedStatement = 'DROP COLUMN test';

		$actualStatement = $dropColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}
}
