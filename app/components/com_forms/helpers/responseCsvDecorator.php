<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

use Hubzero\Utility\Arr;

class ResponseCsvDecorator
{

	/**
	 * Constructs ResponseCsvDecorator instance
	 *
	 * @param    array    $args   Instantiation state
	 * @return   object
	 */
	public static function create($args)
	{
		return new self($args);
	}

	/**
	 * Constructs ResponseCsvDecorator instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args)
	{
		$this->_response = $args['response'];
		$this->_responsesOrder = $args['order'];
		$this->_userId = $this->_response->get('user_id');
		$this->_userHelper = Arr::getValue(
			$args, 'users', new MockProxy(['class' => 'User'])
		);
	}

	/**
	 * Returns CSV data representation of response
	 *
	 * @return   array
	 */
	public function toCsv()
	{
		$metadata = [
			$this->_userId,
			$this->_getUsersName(),
			$this->_getModified()
		];
		$fieldsResponses = $this->_getResponseValues();

		return array_merge($metadata, $fieldsResponses);
	}

	/**
	 * Returns name of response's creator
	 *
	 * @return   string
	 */
	protected function _getUsersName()
	{
		$userId = $this->_userId;
		$user = $this->_userHelper->one($userId);

		return $user->get('name');
	}

	/**
	 * Returns responses modified datetime
	 *
	 * @return   string
	 */
	protected function _getModified()
	{
		return $this->_response->get('modified');
	}

	/**
	 * Collects user's responses to form fields
	 *
	 * @return   array
	 */
	protected function _getResponseValues()
	{
		$usersResponses = $this->_getResponsesByFieldId();

		$responseValues = array_map(function($fieldId) use($usersResponses) {
			if (isset($usersResponses[$fieldId]))
			{
				return $usersResponses[$fieldId]->response;
			}
		}, $this->_responsesOrder);

		return $responseValues;
	}

	/**
	 * Returns field responses mapped by field ID
	 *
	 * @return   array
	 */
	protected function _getResponsesByFieldId()
	{
		$usersResponses = $this->_response->getResponses()->rows()->raw();
		$mappedResponses = [];

		foreach ($usersResponses as $response)
		{
			$fieldId = $response->get('field_id');
			$mappedResponses[$fieldId] = $response;
		}

		return $mappedResponses;
	}

}

