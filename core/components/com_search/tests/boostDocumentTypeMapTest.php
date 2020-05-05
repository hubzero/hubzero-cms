<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostDocumentTypeMap.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\BoostDocumentTypeMap as Map;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class BoostDocumentTypeMapTest extends Basic
{
	use canMock;

	public function testDocumentTypeToFieldDataForCitation()
	{
		$expected = ['field' => 'hubtype', 'field_value' => 'citation'];
		$formattedCitationType = 'Citations';
		$langMock = $this->mock([
			'class' => 'Lang',
		 	'methods' => ['txt' => $formattedCitationType]
		]);
		$map = new Map(['lang' => $langMock]);

		$fieldData = $map->documentTypeToFieldData($formattedCitationType);

		$this->assertEquals($expected, $fieldData);
	}

	public function testDocumentTypeToFieldDataForResource()
	{
		$documentType = 'Tools';
		$expected = ['field' => 'type', 'field_value' => $documentType];
		$map = new Map();

		$fieldData = $map->documentTypeToFieldData($documentType);

		$this->assertEquals($expected, $fieldData);
	}

	public function testgetFormattedFieldValueForCitation()
	{
		$fieldValue = 'citation';
		$map = new Map();
		$expected = $map->getFormattedCitationType();

		$formattedFieldValue = $map->getFormattedFieldValue($fieldValue);

		$this->assertEquals($expected, $formattedFieldValue);
	}

	public function testgetFormattedFieldValueForResource()
	{
		$resourceType = 'Tools';
		$map = new Map();

		$formattedFieldValue = $map->getFormattedFieldValue($resourceType);

		$this->assertEquals($resourceType, $formattedFieldValue);
	}

	public function testGetFormattedCitationType()
	{
		$langMock = $this->mock(['class' => 'Lang', 'methods' => ['txt']]);
		$map = new Map(['lang' => $langMock]);

		$langMock->expects($this->once())
			->method('txt')
			->with('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION');

		$map->getFormattedCitationType();
	}

}
