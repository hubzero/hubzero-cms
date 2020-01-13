<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

require_once "$componentPath/models/form.php";
require_once "$componentPath/models/formResponse.php";

use Components\Forms\Models\Form;
use Components\Forms\Models\FormResponse;
use Hubzero\Utility\Arr;

class RespondentsHelper
{

	/**
	 * Create a RespondentsHelper instance
	 *
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_responsesHelper = Arr::getValue(
			$args, 'responses', new MockProxy(['class' => 'Components\Forms\Models\FormResponse'])
		);
		$this->_usersHelper = Arr::getValue($args, 'users', new MockProxy(['class' => 'User']));
	}

	/**
	 * Returns users' emails
	 *
	 * @param    array   $responseIds   Responses' IDs
	 * @return   array
	 */
	public function getEmails($responseIds)
	{
		$userIds = $this->_getUserIds($responseIds);

		return $this->_getEmails($userIds);
	}

	/**
	 * Returns users' IDs
	 *
	 * @param    array   $responseIds   Responses' IDs
	 * @return   array
	 */
	protected function _getUserIds($responseIds)
	{
		$userIds = $this->_responsesHelper->all()
			->select('user_id')
			->whereIn('id', $responseIds)
			->rows()
			->toArray();

		$userIds = array_map(function($responseData) {
			return $responseData['user_id'];
		}, $userIds);

		return $userIds;
	}

	/**
	 * Returns users' emails
	 *
	 * @param    array   $userIds   Users' IDs
	 * @return   array
	 */
	protected function _getEmails($userIds)
	{
		$userEmails = $this->_usersHelper->all()
			->select('email')
			->whereIn('id', $userIds)
			->rows()
			->toArray();

		$userEmails = array_map(function($responseData) {
			return $responseData['email'];
		}, $userEmails);

		return $userEmails;
	}

}
