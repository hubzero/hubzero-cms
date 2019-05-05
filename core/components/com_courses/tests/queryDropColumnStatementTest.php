<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests;

$componentPath = Component::path('com_courses');

require_once "$componentPath/helpers/queryDropColumnStatement.php";

use Hubzero\Test\Basic;
use Components\Courses\Helpers\QueryDropColumnStatement;

class QueryDropColumnStatementTest extends Basic
{

	public function testToStringReturnsCorrectStringWhenNameAndTypeProvided()
	{
		$columnData = ['name' => 'test'];
		$dropColumnStatement = new QueryDropColumnStatement($columnData);
		$expectedStatement = 'DROP COLUMN test';

		$actualStatement = $dropColumnStatement->toString();

		$this->assertEquals($expectedStatement, $actualStatement);
	}

}
