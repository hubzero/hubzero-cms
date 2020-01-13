<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

$componentPath = Component::path('com_forms');

require_once "$componentPath/models/formPage.php";
require_once "$componentPath/models/formPrerequisite.php";
require_once "$componentPath/models/formResponse.php";
require_once "$componentPath/models/pageField.php";

use Components\Forms\Models\FormResponse;
use Components\Forms\Models\PageField;
use Components\Forms\Traits\possessable;
use Hubzero\Database\Relational;

class Form extends Relational
{
	use possessable;

	static protected $_pageClass = 'Components\Forms\Models\FormPage';
	static protected $_prerequisiteClass = 'Components\Forms\Models\FormPrerequisite';
	static protected $_responseClass = 'Components\Forms\Models\FormResponse';

	protected $table = '#__forms_forms';
	protected $_ownerForeignKey = 'created_by';

	/*
	 * Attributes to be populated on record creation
	 *
	 * @var array
	 */
	public $initiate = ['created'];

	/*
	 * Attribute validation
	 *
	 * @var  array
	 */
	public $rules = [
		'name' => 'notempty',
		'opening_time' => 'notempty',
		'closing_time' => 'notempty'
	];

	/**
	 * Determines if users responses to prereqs were accepted
	 *
	 * @param    int    $userId   User's ID
	 * @return   object
	 */
	public function prereqsAccepted($userId)
	{
		$prereqs = $this->getPrerequisites()
			->rows();
		$prereqsAccepted = true;

		foreach ($prereqs as $prereq)
		{
			if (!$prereq->acceptedFor($userId))
			{
				$prereqsAccepted = false;
				break;
			}
		}

		return $prereqsAccepted;
	}

	/**
	 * Returns associated prerequisites
	 *
	 * @return   object
	 */
	public function getPrerequisites()
	{
		$prerequisiteModelClass = self::$_prerequisiteClass;
		$foreignKey = 'form_id';

		$prerequisites = $this->oneToMany($prerequisiteModelClass, $foreignKey);

		return $prerequisites;
	}

	/**
	 * Indicates if given user response was accepted
	 *
	 * @param    int    $userId   User's ID
	 * @return   bool
	 */
	public function acceptedFor($userId)
	{
		$response = $this->getResponse($userId);

		return !!$response->get('accepted');
	}

	/**
	 * Returns associated responses
	 *
	 * @param    int      $userId Given user's ID
	 * @return   object
	 */
	public function getResponse($userId)
	{
		$response = self::_getResponse($this->get('id'), $userId);

		return $response;
	}

	/**
	 * Searches for response with given form and user IDs
	 *
	 * @param    int      $formId   Given form's ID
	 * @param    int      $userId   Given user's ID
	 * @return   object
	 */
	protected static function _getResponse($formId, $userId)
	{
		$responseClass = self::_getResponseClass();

		$response = $responseClass::oneWhere([
			'form_id' => $formId,
			'user_id' => $userId
		]);

		return $response;
	}

	/**
	 * Returns form response model class
	 *
	 * @return   object
	 */
	protected static function _getResponseClass()
	{
		return get_class(FormResponse::blank());
	}

	/**
	 * Get associated fields in order by page
	 *
	 * @return   array
	 */
	public function getFieldsOrdered()
	{
		$pages = $this->getPages()->order('order', 'asc')->rows();
		$orderedFields = [];

		foreach ($pages as $page)
		{
			$fields = $page->getFields()->order('order', 'asc');

			foreach ($fields as $field)
			{
				$orderedFields[$field->get('id')] = $field;
			}
		}

		return $orderedFields;
	}

	/**
	 * Get required fields
	 *
	 * @return   object
	 */
	public function getRequiredFields()
	{
		$allFields = $this->getFields();

		$requiredFields = $allFields->whereEquals('required', 1);

		return $requiredFields;
	}

	/**
	 * Get associated fields
	 *
	 * @return   object
	 */
	public function getFields()
	{
		$pagesIds = $this->_getPagesIds();
		$fieldClass = self::_getFieldClass();

		$fields = $fieldClass::all()
			->whereIn('page_id', $pagesIds);

		return $fields;
	}

	/**
	 * Returns associated pages IDs
	 *
	 * @return   array
	 */
	protected function _getPagesIds()
	{
		$pages = $this->getPages()->select('id')->execute();

		$pagesIds = array_map(function($page) {
			return $page->id;
		}, $pages);

		return $pagesIds;
	}

	/**
	 * Returns field class
	 *
	 * @return   object
	 */
	protected static function _getFieldClass()
	{
		$fieldClass = get_class(PageField::blank());

		return $fieldClass;
	}

	/**
	 * Get associated pages
	 *
	 * @return   object
	 */
	public function getPages()
	{
		$pageModelClass = self::$_pageClass;
		$foreignKey = 'form_id';

		$pages = $this->oneToMany($pageModelClass, $foreignKey);

		return $pages;
	}

	/**
	 * Retrieves page at given location
	 *
	 * @param    int      $position   Ordinal position
	 * @return   object
	 */
	public function getPageOrdinal($position)
	{
		$pages = $this->getPages()
			->order('order', 'asc')
			->rows()
			->raw();

		if (!empty($pages))
		{
			$pageArray = array_slice($pages, $position - 1, 1);
			$page = $pageArray[0];
		}
		else
		{
			$pageClass = self::$_pageClass;
			$page = $pageClass::blank();
		}

		return $page;
	}

	/**
	 * Returns associated page models in an array
	 *
	 * @return   array
	 */
	public function getPagesInArray()
	{
		$pagesInArray = $this->_getAssociationsInArray('getPages');

		return $pagesInArray;
	}

	/**
	 * Returns associated prerequisite models in an array
	 *
	 * @return   array
	 */
	public function getPrereqsInArray()
	{
		$prereqsInArray = $this->_getAssociationsInArray('getPrerequisites');

		return $prereqsInArray;
	}

	/**
	 * Determines if given page is form's last page
	 *
	 * @param    object   $page   Page record
	 * @return   bool
	 */
	public function isLastPage($page)
	{
		$pageId = $page->get('id');

		$isLast = $this->getPages()
			->order('order', 'asc')
			->rows()
			->isLast($pageId);

		return $isLast;
	}

	/**
	 * Get associations in array
	 *
	 * @param    string   $associationFunc   Name of association getter
	 * @return   array
	 */
	protected function _getAssociationsInArray($associationFunc)
	{
		$associatons = [];
		$models = $this->$associationFunc()->rows();

		foreach ($models as $model)
		{
			$associatons[] = $model;
		}

		return $associatons;
	}

	/**
	 * Returns all possible form prerequisites for form with given ID
	 *
	 * @param    object   $form   Form record
	 * @return   object
	 */
	public static function possiblePrereqsFor($form)
	{
		$formId = $form->get('id');

		$possiblePrereqs = self::all()
			->select('id, name')
			->whereNotIn('id', [$formId])
			->order('name', 'asc');

		return $possiblePrereqs;
	}

	/**
	 * Indicates if form should be disabled for given user
	 *
	 * @param    int    $userId   User ID
	 * @return   bool
	 */
	public function isDisabledFor($userId)
	{
		$isActive = $this->isActive();
		$responsesLocked = $this->get('responses_locked');
		$submitted = $this->responseSubmittedBy($userId);

		return !$isActive || ($submitted && $responsesLocked);
	}

	/**
	 * Indicates if form is active based on time bounds and disabled status
	 *
	 * @return   bool
	 */
	public function isActive()
	{
		$isClosed = $this->isClosed();
		$isOpen = $this->isOpen();
		$isDisabled = $this->get('disabled');

		return !$isDisabled && $isOpen && !$isClosed;
	}

	/**
	 * Indicates if form is closed based on closing time
	 *
	 * @return   bool
	 */
	public function isClosed()
	{
		$closingTime = strtotime($this->get('closing_time'));

		return time() > $closingTime;
	}

	/**
	 * Indicates if form is open based on opening time
	 *
	 * @return   bool
	 */
	public function isOpen()
	{
		$openingTime = strtotime($this->get('opening_time'));

		return time() > $openingTime;
	}

	/**
	 * Indicates if given user has submitted response for form
	 *
	 * @param    int    $userId   User ID
	 * @return   bool
	 */
	public function responseSubmittedBy($userId)
	{
		$response = $this->getResponse($userId);

		return !!$response->get('submitted');
	}

	/**
	 * Get associated responses
	 *
	 * @return   object
	 */
	public function getResponses()
	{
		$responseModelClass = self::$_responseClass;
		$foreignKey = 'form_id';

		$responses = $this->oneToMany($responseModelClass, $foreignKey);

		return $responses;
	}

	/**
	 * Calculates number of days until form opens
	 *
	 * @return   float
	 */
	public function getDaysUntilOpen()
	{
		return $this->_secondsToDays($this->_getSecondsUntilOpen());
	}

	/**
	 * Calculates number of days until form closes
	 *
	 * @return   float
	 */
	public function getDaysUntilClose()
	{
		return $this->_secondsToDays($this->_getSecondsUntilClose());
	}

	/**
	 * Calculates number of days since form closed
	 *
	 * @return   float
	 */
	public function getDaysSinceClose()
	{
		return $this->_secondsToDays($this->_getSecondsSinceClose());
	}

	/**
	 * Calculates number of seconds until form opens
	 *
	 * @return   float
	 */
	protected function _getSecondsUntilOpen()
	{
		$openingTime = strtotime($this->get('opening_time'));

		return $openingTime - time();
	}

	/**
	 * Calculates number of seconds since form closed
	 *
	 * @return   float
	 */
	protected function _getSecondsSinceClose()
	{
		return -$this->_getSecondsUntilClose();
	}

	/**
	 * Calculates number of seconds until form closes
	 *
	 * @return   float
	 */
	protected function _getSecondsUntilClose()
	{
		$closingTime = strtotime($this->get('closing_time'));

		return $closingTime - time();
	}

	/**
	 * Converts seconds to days
	 *
	 * @return   float
	 */
	protected function _secondsToDays($seconds)
	{
		$secondsPerDay = 60 * 60 * 24;

		return $seconds / $secondsPerDay;
	}

}
