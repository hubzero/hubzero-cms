<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/models/responseActivity.php";

use Components\Forms\Helpers\MockProxy;
use Components\Forms\Models\ResponseActivity;
use Hubzero\Utility\Arr;

class FormResponseActivityHelper
{

	/**
	 * Constructs an FormResponseActivityHelper instance
	 *
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_activityFactory = Arr::getValue(
			$args, 'factory', new MockProxy([
			'class' => 'Components\Forms\Models\ResponseActivity'
		]));
	}

	/**
	 * Logs start of form response
	 *
	 * @param    int    $responseId   Given response's ID
	 * @return   void
	 */
	public function logStart($responseId)
	{
		$this->_logActivity('started', $responseId);
	}

	/**
	 * Logs submission of form response
	 *
	 * @param    int    $responseId   Given response's ID
	 * @return   void
	 */
	public function logSubmit($responseId)
	{
		$this->_logActivity('submitted', $responseId);
	}

	/**
	 * Logs acceptance of form response
	 *
	 * @param    int     $responseId   Given response's ID
	 * @param    bool    $isAccepted   Acceptance status
	 * @return   void
	 */
	public function logReview($responseId, $isAccepted)
	{
		$activity = $isAccepted ? 'accepted' : 'reversed';

		$this->_logActivity($activity, $responseId);
	}

	/**
	 * Records given activity
	 *
	 * @param    string   $activity     Activity description
	 * @param    int      $responseId   Associated response's ID
	 * @return   void
	 */
	protected function _logActivity($activity, $responseId)
	{
		$responseActivity = $this->_activityFactory->blank();

		$responseActivity->set([
			'action' => 'activity',
			'description' => $activity,
			'scope_id' => $responseId
		]);

		$responseActivity->save();
	}

}
