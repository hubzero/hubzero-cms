<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/boostDocumentTypeMap.php";
require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/traits/isUnique.php";

use Components\Search\Helpers\BoostDocumentTypeMap as Map;
use Components\Search\Helpers\MockProxy;
use Components\Search\Traits\isUnique;
use Hubzero\Database\Relational;
use Hubzero\Utility\Arr;

/**
 * Database model for Solr Boost
 *
 * @uses  \Hubzero\Database\Relational
 */
class Boost extends Relational
{
	use isUnique;

	protected static $uniqueKeys = [['field', 'field_value']];

	public $initiate = ['created'];

	protected $rules = [
		'field' => 'notempty',
		'field_value' => 'notempty'
	];

	protected $table = '#__solr_search_boosts';

	protected $map,
		$userHelper;

	public function __construct($args = [])
	{
		$this->map = Arr::getValue($args, 'map', new Map());
		$this->userHelper = Arr::getValue(
			$args, 'user', new MockProxy(['class' => 'User'])
		);

		parent::__construct();
	}

	public function getId()
	{
		return $this->get('id');
	}

	public function getField()
	{
		return $this->get('field');
	}

	public function getFormattedFieldValue()
	{
		$fieldValue = $this->getFieldValue();

		return $this->map->getFormattedFieldValue($fieldValue);
	}

	public function getFieldValue()
	{
		return $this->get('field_value');
	}

	public function getStrength()
	{
		return $this->get('strength');
	}

	public function getCreated()
	{
		return $this->get('created');
	}

	public function getCreatedBy()
	{
		return $this->get('created_by');
	}

	public function getAuthor()
	{
		$createdBy = $this->getCreatedBy();

		return $this->userHelper->one($createdBy);
	}

	public function save()
	{
		if ($this->isNew() && !$this->isUnique())
		{
			$documentType = $this->getFormattedFieldValue();
			$this->addError(Lang::txt('COM_SEARCH_BOOST_ERROR_NON_UNIQUE', $documentType));
			$saved = false;
		}
		else
		{
			$saved = parent::save();
		}

		return $saved;
	}

}
