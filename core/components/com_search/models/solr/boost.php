<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;

/**
 * Database model for Solr Boost
 *
 * @uses  \Hubzero\Database\Relational
 */
class Boost extends Relational
{
	public $initiate = ['created'];

	protected $rules = [
		'field' => 'notempty',
		'field_value' => 'notempty'
	];

	protected $table = '#__solr_search_boosts';

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

		switch ($fieldValue)
		{
			case 'citation':
				$formattedFieldValue = Lang::txt('COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION');
				break;
			default:
				$formattedFieldValue = $fieldValue;
		}

		return $formattedFieldValue;
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

}
