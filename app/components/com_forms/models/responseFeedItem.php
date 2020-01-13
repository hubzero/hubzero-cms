<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/formResponse.php";

use Components\Forms\Models\FormResponse;
use Hubzero\Activity\Log;

class ResponseFeedItem extends Log
{

	/**
	 * Validation rules
	 *
	 * @var   array
	 */
	protected $rules = ['description' => 'notempty'];

	protected $table = '#__activity_logs';

	/**
	 * Form responses activity scope
	 *
	 * @var   string
	 */
	protected	static $ACTIVITY_SCOPE = 'forms.responses';

	/**
	 * Retrieves all activity items in form response scope
	 *
	 * @return   object
	 */
	public static function all($columns = null)
	{
		return parent::all()
			->whereEquals('scope', self::$ACTIVITY_SCOPE);
	}

	/**
	 * Returns all activity items for response w/ given ID
	 *
	 * @param    int      $responseId   Response record's ID
	 * @return   object
	 */
	public static function allForResponse($responseId)
	{
		return self::all()
			->whereEquals('scope_id', $responseId);
	}

	/**
	 * Returns all activity items for user w/ given ID
	 *
	 * @param    int      $userId   User record's ID
	 * @return   object
	 */
	public static function allForUser($userId)
	{
		$responses = FormResponse::allForUser($userId)->rows();

		$responseIds = $responses->fieldsByKey('id');

		return self::all()
			->whereIn('scope_id', $responseIds);
	}

	/**
	 * Accessor for associated form's name
	 *
	 * @return   int
	 */
	public function getFormName()
	{
		$form = $this->getForm();

		return $form->get('name');
	}

	/**
	 * Accessor for associated form
	 *
	 * @return   object
	 */
	public function getForm()
	{
		$response = $this->getResponse();

		return $response->getForm();
	}

	/**
	 * Accessor for associated response's ID
	 *
	 * @return   int
	 */
	public function getResponseId()
	{
		$response = $this->getResponse();

		return $response->get('id');
	}

	/**
	 * Accessor for associated response
	 *
	 * @return   object
	 */
	public function getResponse()
	{
		$responseId = $this->get('scope_id');

		return FormResponse::one($responseId);
	}

}
