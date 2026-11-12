<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Helpers\BoostDocumentTypeMap as Map;
use Components\Search\Tests\Traits\CanMock;
use Hubzero\Test\Basic;
use Component;

class BoostDocumentTypeMapTest extends Basic
{
    use CanMock;

    public function testDocumentTypeToFieldDataForCitation(): void
    {
        $expected = ['field' => 'hubtype', 'field_value' => 'citation'];
        $formattedCitationType = 'Citations';
        $langMock = $this->mock([
            'class' => 'Lang',
            'methods' => ['txt' => $formattedCitationType],
        ]);
        $map = new Map(['lang' => $langMock]);

        $fieldData = $map->documentTypeToFieldData($formattedCitationType);

        $this->assertEquals($expected, $fieldData);
    }

    public function testDocumentTypeToFieldDataForResource(): void
    {
        $documentType = 'Tools';
        $expected = ['field' => 'type', 'field_value' => $documentType];
        $map = new Map();

        $fieldData = $map->documentTypeToFieldData($documentType);

        $this->assertEquals($expected, $fieldData);
    }

    public function testgetFormattedFieldValueForCitation(): void
    {
        $fieldValue = 'citation';
        $map = new Map();
        $expected = $map->getFormattedCitationType();

        $formattedFieldValue = $map->getFormattedFieldValue($fieldValue);

        $this->assertEquals($expected, $formattedFieldValue);
    }

    public function testgetFormattedFieldValueForResource(): void
    {
        $resourceType = 'Tools';
        $map = new Map();

        $formattedFieldValue = $map->getFormattedFieldValue($resourceType);

        $this->assertEquals($resourceType, $formattedFieldValue);
    }

    public function testGetFormattedCitationType(): void
    {
        $langMock = $this->mock(['class' => 'Lang', 'methods' => ['txt']]);
        $map = new Map(['lang' => $langMock]);

        $langMock
            ->expects($this->once())
            ->method('txt')
            ->with('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION');

        $map->getFormattedCitationType();
    }
}
