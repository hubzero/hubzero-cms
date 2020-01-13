<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/relationalQueryHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Components\Forms\Helpers\RelationalQueryHelper;
use Components\Forms\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class RelationalQueryHelperTest extends Basic
{
	use canMock;

	public function testFlatMapReturnsCorrectMap()
	{
		$rowA = $this->mock(['class' => 'Relational', 'methods' => ['get' => 2]]);
		$rowB = $this->mock(['class' => 'Relational', 'methods' => ['get' => 1]]);
		$rowC = $this->mock(['class' => 'Relational', 'methods' => ['get' => 0]]);
		$query = new \ArrayIterator([$rowA, $rowB, $rowC]);
		$helper = new RelationalQueryHelper();

		$flatMap = $helper->flatMap($query, 'id');

		$this->assertEquals([2, 1, 0], $flatMap);
	}

}
