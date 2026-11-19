<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Hubzero\Utility\Arr;
use Hubzero\Facades\Lang;

class TypeOptionsHelper
{
    /**
     * @var  mixed
     */
    protected $typeHelper = null;

    /**
     * @param   array<mixed>  $args
     */
    public function __construct($args = [])
    {
        $this->typeHelper = Arr::getValue(
            $args,
            'type',
            new MockProxy([
                'class' => 'Components\Resources\Models\Type',
            ]),
        );
    }

    /**
     * @return  array<int, mixed>
     */
    public function getAllSorted()
    {
        $allTypes = $this->getAll();

        sort($allTypes);

        return $allTypes;
    }

    /**
     * @return  array<mixed>
     */
    public function getAll()
    {
        $resourceTypes = $this->typeHelper
            ->all()
            ->rows()
            ->fieldsByKey('type');

        $supplementaryTypes = [
            Lang::txt('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION'),
        ];

        $allTypes = array_merge($resourceTypes, $supplementaryTypes);

        return $allTypes;
    }
}
