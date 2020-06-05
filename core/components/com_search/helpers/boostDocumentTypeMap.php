<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/mockProxy.php";

use Components\Search\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class BoostDocumentTypeMap
{

	protected $lang;

	public function __construct($args = [])
	{
		$this->lang = Arr::getValue(
			$args, 'lang', new MockProxy(['class' => 'Lang'])
		);
	}

	public function documentTypeToFieldData($documentType)
	{
		switch ($documentType)
		{
			case $this->getFormattedCitationType():
				$field = 'hubtype';
				$fieldValue = 'citation';
				break;
			default:
				$field = 'type';
				$fieldValue = $documentType;
		}

		return [
			'field' => $field,
			'field_value' => $fieldValue
		];
	}

	public function getFormattedFieldValue($fieldValue)
	{
		switch ($fieldValue)
		{
			case 'citation':
				$formattedValue = $this->getFormattedCitationType();
				break;
			default:
				$formattedValue = $fieldValue;
		}

		return $formattedValue;
	}

	public function getFormattedCitationType()
	{
		$langKey = 'COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION';

		return $this->lang->txt($langKey);
	}

}
