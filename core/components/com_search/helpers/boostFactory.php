<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostDocumentTypeMap.php";
require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/models/solr/boost.php";

use Components\Search\Helpers\BoostDocumentTypeMap as Map;
use Components\Search\Helpers\MockProxy;
use Components\Search\Models\Solr\Boost;
use Hubzero\Utility\Arr;

class BoostFactory
{

	public function __construct($args = [])
	{
		$this->_map = Arr::getValue($args, 'map', new Map());
		$this->_userHelper = Arr::getValue(
			$args, 'user', new MockProxy(['class' => 'User'])
		);
	}

	public function one($boostData)
	{
		$boost = Boost::blank();

		$formedData = $this->_formData($boostData);
		$boost->set($formedData);

		return $boost;
	}

	protected function _formData($boostData)
	{
		$documentType = $boostData['document_type'];
		$documentProperties = $this->_map->documentTypeToFieldData($documentType);
		$creationProperties = $this->_generateCreationProperties();

		$formedData = array_merge($boostData, $documentProperties, $creationProperties);

		return $formedData;
	}

	protected function _generateCreationProperties()
	{
		$userId = $this->_userHelper->get('id');
		$now = Date::toSql();

		return [
			'created_by' => $userId,
			'created' => $now
		];
	}

}
