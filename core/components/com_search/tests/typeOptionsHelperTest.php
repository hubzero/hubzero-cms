<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Helpers\TypeOptionsHelper as Helper;
use Components\Search\Tests\Traits\CanMock;
use Hubzero\Test\Basic;
use Component;

class TypeOptionsHelperTest extends Basic
{
    use CanMock;

    public function testGetAll(): void
    {
        $resourceTypes = ['A', 'B'];
        $rowsMock = $this->mock([
            'class' => 'Rows',
            'methods' => [
                'fieldsByKey' => $resourceTypes,
            ],
        ]);
        $relationalMock = $this->mock([
            'class' => 'Relational',
            'methods' => ['rows' => $rowsMock],
        ]);
        $typeMock = $this->mock([
            'class' => 'Type',
            'methods' => ['all' => $relationalMock],
        ]);
        $helper = new Helper(['type' => $typeMock]);
        $supplementaryTypes = ['COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION'];
        $expected = array_merge($resourceTypes, $supplementaryTypes);

        $allTypes = $helper->getAll();

        $this->assertEquals($expected, $allTypes);
    }

    public function testGetAllSorted(): void
    {
        $resourceTypes = ['D', 'C'];
        $rowsMock = $this->mock([
            'class' => 'Rows',
            'methods' => [
                'fieldsByKey' => $resourceTypes,
            ],
        ]);
        $relationalMock = $this->mock([
            'class' => 'Relational',
            'methods' => ['rows' => $rowsMock],
        ]);
        $typeMock = $this->mock([
            'class' => 'Type',
            'methods' => ['all' => $relationalMock],
        ]);
        $helper = new Helper(['type' => $typeMock]);
        $supplementaryTypes = ['COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION'];
        $expected = array_merge($resourceTypes, $supplementaryTypes);
        sort($expected);

        $allTypes = $helper->getAllSorted();

        $this->assertEquals($expected, $allTypes);
    }
}
