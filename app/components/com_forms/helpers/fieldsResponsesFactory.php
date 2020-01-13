<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$compnentPath = Component::path('com_forms');

require_once "$componentPath/helpers/factory.php";

use Components\Forms\Helpers\Factory;

class FieldsResponsesFactory extends Factory
{

	/**
	 * Constructs FieldsResponsesFactory instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$args['model_name'] = 'Components\Forms\Models\FieldResponse';

		parent::__construct($args);
	}

	/**
	 * Updates fields' responses
	 *
	 * @param    array    $submittedResponsesData   Submitted responses data
	 * @return   object
	 */
	public function updateFieldsResponses($currentResponses, $submittedResponsesData)
	{
		$parsedData = $this->_parseResponsesData($submittedResponsesData);

		$augmentedData = $this->_addModifiedIfAltered($parsedData);

		return parent::batchUpdate($currentResponses, $augmentedData);
	}

	/**
	 * Parses given responses' data
	 *
	 * @return array
	 */
	protected function _parseResponsesData($responsesData)
	{
		$parsedData = array_map(function($data) {
			return $this->_parseResponseData($data);
		}, $responsesData);

		return $parsedData;
	}

	/**
	 * Parses given response's data
	 *
	 * @return array
	 */
	protected function _parseResponseData($responseData)
	{
		$parsedData = $responseData;
		$parsedData['user_id'] = User::get('id');

		if (!isset($responseData['response']))
		{
			$parsedData['response'] = null;
		}
		else if (is_array($responseData['response']))
		{
			$parsedData['response'] = json_encode($responseData['response']);
		}

		return $parsedData;
	}

}
