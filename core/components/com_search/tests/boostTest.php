<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/models/solr/boost.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Models\Solr\Boost;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class BoostTest extends Basic
{
	use canMock;

	public function testGetIdReturnsId()
	{
		$id = 96;
		$boost = Boost::blank();

		$boost->set('id', $id);
		$boostId = $boost->getId();

		$this->assertEquals($id, $boostId);
	}

	public function testGetFieldReturnsField()
	{
		$field = 'field';
		$boost = Boost::blank();

		$boost->set('field', $field);
		$boostField = $boost->getField();

		$this->assertEquals($field, $boostField);
	}

	public function testGetFormattedFieldValueReturnsCorrectValueForCitations()
	{
		$langMock = $this->mock(['class' => 'Lang', 'methods' => ['txt']]);
		$boost = new Boost(['lang' => $langMock]);

		$boost->set('field_value', 'citation');

		$langMock->expects($this->once())
			->method('txt')
			->with('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION');

		$boost->getFormattedFieldValue();
	}

	public function testGetFieldValueReturnsFieldValue()
	{
		$fieldValue = 'value';
		$boost = Boost::blank();

		$boost->set('field_value', $fieldValue);
		$boostFieldValue = $boost->getFieldValue();

		$this->assertEquals($fieldValue, $boostFieldValue);
	}

	public function testGetStrengthReturnsStrength()
	{
		$strength = -23;
		$boost = Boost::blank();

		$boost->set('strength', $strength);
		$boostStrength = $boost->getStrength();

		$this->assertEquals($strength, $boostStrength);
	}

	public function testGetCreatedReturnsCreated()
	{
		$created = '1969-01-01 00:00:00';
		$boost = Boost::blank();

		$boost->set('created', $created);
		$boostCreated = $boost->getCreated();

		$this->assertEquals($created, $boostCreated);
	}

	public function testGetCreatedByReturnsCreatedBy()
	{
		$createdBy = 1033;
		$boost = Boost::blank();

		$boost->set('created_by', $createdBy);
		$boostCreatedBy = $boost->getCreatedBy();

		$this->assertEquals($createdBy, $boostCreatedBy);
	}

}
