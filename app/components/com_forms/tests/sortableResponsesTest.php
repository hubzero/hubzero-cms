<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/sortableResponses.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\SortableResponses;
use Components\Forms\Tests\Traits\canMock;

class SortableResponsesTest extends Basic
{
	use canMock;

	public function testOrderForwardsOrderToResponsesByDefault()
	{
		$direction = 'asc';
		$field = 'test';
		$responses = $this->mock([
			'class' => 'Relational', 'methods' => ['order']
		]);
		$responses->pagination = null;
		$sortableResponses = new SortableResponses(['responses' => $responses]);

		$responses->expects($this->once())
			->method('order')
			->with($field, $direction);

		$sortableResponses->order($field, $direction);
	}

	public function testOrderingByCompletionPercentageCorrectlyOrdersRecords()
	{
		$rowA = $this->mock([
			'class' => 'Relational', 'methods' => ['requiredCompletionPercentage' => 0]
		]);
		$rowB = $this->mock([
			'class' => 'Relational', 'methods' => ['requiredCompletionPercentage' => 89]
		]);
		$rowC = $this->mock([
			'class' => 'Relational', 'methods' => ['requiredCompletionPercentage' => 90]
		]);
		$rows = $this->mock([
				'class' => 'Rows', 'methods' => ['raw' => [$rowA, $rowB, $rowC]]
		]);
		$sortedRows = [$rowC, $rowB, $rowA];
		$responses = $this->mock([
			'class' => 'Relational',
			'methods' => ['rows' => $rows]
		]);
		$responses->pagination = null;

		$sortableResponses = new SortableResponses(['responses' => $responses]);
		$sortableResponses->order('completion_percentage', 'desc');

		$this->assertEquals($sortedRows, $sortableResponses->rows());
	}

}
