<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Models\Solr\Boost;
use Components\Search\Tests\Traits\CanMock;
use Hubzero\Test\Basic;
use Component;

class BoostTest extends Basic
{
    use CanMock;

    public function testGetIdReturnsId(): void
    {
        $id = 96;
        $boost = Boost::blank();

        $boost->set('id', $id);
        $boostId = $boost->getId();

        $this->assertEquals($id, $boostId);
    }

    public function testGetFieldReturnsField(): void
    {
        $field = 'field';
        $boost = Boost::blank();

        $boost->set('field', $field);
        $boostField = $boost->getField();

        $this->assertEquals($field, $boostField);
    }

    public function testGetFormattedFieldValueReturnsCorrectValueForCitations(): void
    {
        $mapMock = $this->mock([
            'class' => 'Map',
            'methods' => ['getFormattedFieldValue'],
        ]);
        $boost = new Boost(['map' => $mapMock]);

        $boost->set('field_value', 'citation');

        $mapMock
            ->expects($this->once())
            ->method('getFormattedFieldValue');

        $boost->getFormattedFieldValue();
    }

    public function testGetFieldValueReturnsFieldValue(): void
    {
        $fieldValue = 'value';
        $boost = Boost::blank();

        $boost->set('field_value', $fieldValue);
        $boostFieldValue = $boost->getFieldValue();

        $this->assertEquals($fieldValue, $boostFieldValue);
    }

    public function testGetStrengthReturnsStrength(): void
    {
        $strength = -23;
        $boost = Boost::blank();

        $boost->set('strength', $strength);
        $boostStrength = $boost->getStrength();

        $this->assertEquals($strength, $boostStrength);
    }

    public function testGetCreatedReturnsCreated(): void
    {
        $created = '1969-01-01 00:00:00';
        $boost = Boost::blank();

        $boost->set('created', $created);
        $boostCreated = $boost->getCreated();

        $this->assertEquals($created, $boostCreated);
    }

    public function testGetCreatedByReturnsCreatedBy(): void
    {
        $createdBy = 1033;
        $boost = Boost::blank();

        $boost->set('created_by', $createdBy);
        $boostCreatedBy = $boost->getCreatedBy();

        $this->assertEquals($createdBy, $boostCreatedBy);
    }

    public function testGetAuthorInvokesOne(): void
    {
        $userId = 987;
        $userMock = $this->mock([
            'class' => 'User',
            'methods' => ['one'],
        ]);
        $boost = new Boost(['user' => $userMock]);
        $boost->set('created_by', $userId);

        $userMock
            ->expects($this->once())
            ->method('one')
            ->with($userId);

        $boost->getAuthor();
    }
}
