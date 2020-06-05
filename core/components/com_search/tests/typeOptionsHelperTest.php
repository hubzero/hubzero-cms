<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/typeOptionsHelper.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\TypeOptionsHelper as Helper;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class TypeOptionsHelperTest extends Basic
{
	use canMock;

	public function testGetAll()
	{
		$resourceTypes = ['A', 'B'];
		$rowsMock = $this->mock([
			'class' => 'Rows',
			'methods' => [
				'fieldsByKey' => $resourceTypes
			]
		]);
		$relationalMock = $this->mock([
			'class' => 'Relational',
			'methods' => ['rows' => $rowsMock]
		]);
		$typeMock = $this->mock([
			'class' => 'Type',
		 	'methods' => ['all' => $relationalMock]
		]);
		$helper = new Helper(['type' => $typeMock]);
		$supplementaryTypes = ['COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION'];
		$expected = array_merge($resourceTypes, $supplementaryTypes);

		$allTypes = $helper->getAll();

		$this->assertEquals($expected, $allTypes);
	}

	public function testGetAllSorted()
	{
		$resourceTypes = ['D', 'C'];
		$rowsMock = $this->mock([
			'class' => 'Rows',
			'methods' => [
				'fieldsByKey' => $resourceTypes
			]
		]);
		$relationalMock = $this->mock([
			'class' => 'Relational',
			'methods' => ['rows' => $rowsMock]
		]);
		$typeMock = $this->mock([
			'class' => 'Type',
		 	'methods' => ['all' => $relationalMock]
		]);
		$helper = new Helper(['type' => $typeMock]);
		$supplementaryTypes = ['COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION'];
		$expected = array_merge($resourceTypes, $supplementaryTypes);
		sort($expected);

		$allTypes = $helper->getAllSorted();

		$this->assertEquals($expected, $allTypes);
	}

}
