<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Components\Search\Helpers\BoostDocumentTypeMap as Map;
use Components\Search\Helpers\MockProxy;
use Components\Search\Models\Solr\Boost;
use Hubzero\Utility\Arr;

class BoostFactory
{
    protected $map = null;

    protected $userHelper = null;

    public function __construct($args = [])
    {
        $this->map = Arr::getValue($args, 'map', new Map());
        $this->userHelper = Arr::getValue(
            $args,
            'user',
            new MockProxy(['class' => 'User'])
        );
    }

    public function one($boostData)
    {
        $boost = Boost::blank();

        $formedData = $this->formData($boostData);
        $boost->set($formedData);

        return $boost;
    }

    protected function formData($boostData)
    {
        $documentType = $boostData['document_type'];
        $documentProperties = $this->map->documentTypeToFieldData($documentType);
        $creationProperties = $this->generateCreationProperties();

        $formedData = array_merge($boostData, $documentProperties, $creationProperties);

        return $formedData;
    }

    protected function generateCreationProperties()
    {
        $userId = $this->userHelper->get('id');
        $now = Date::toSql();

        return [
            'created_by' => $userId,
            'created' => $now
        ];
    }
}
