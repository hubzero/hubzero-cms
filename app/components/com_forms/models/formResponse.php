<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

$componentPath = Component::path('com_forms');
$tagComponentPath = Component::path('com_tags');

require_once "$componentPath/helpers/relationalQueryHelper.php";
require_once "$componentPath/models/fieldResponse.php";
require_once "$componentPath/traits/possessable.php";
require_once "$tagComponentPath/models/tag.php";

use Components\Forms\Helpers\RelationalQueryHelper;
use Components\Forms\Traits\possessable;
use Hubzero\Database\Relational;

class FormResponse extends Relational
{
	use possessable;

	static protected $_fieldResponseClass = 'Components\Forms\Models\FieldResponse';
	static protected $_formClass = 'Components\Forms\Models\Form';
	static protected $_tagClass = 'Components\Tags\Models\Tag';

	protected $table = '#__forms_form_responses';
	protected $_ownerForeignKey = 'user_id';

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
		'form_id' => 'notempty',
		'user_id' => 'notempty'
	];

	/**
	 * Constructs FormResponse instance
	 *
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_relationalHelper = new RelationalQueryHelper();

		parent::__construct();
	}

	/**
	 * Returns record based on given criteria
	 *
	 * @param    array   $criteria   Criteria to match record to
	 * @return   object
	 */
	public static function oneWhere($criteria)
	{
		$record = self::_getRecordWhere($criteria);

		if (!$record)
		{
			$record = self::blank();
		}

		return $record;
	}

	/**
	 * Searches for record using given criteria
	 *
	 * @param    array   $criteria   Criteria to match record to
	 * @return   mixed
	 */
	protected static function _getRecordWhere($criteria)
	{
		$query = self::all();

		foreach ($criteria as $attr => $value)
		{
			$query->whereEquals($attr, $value);
		}

		return $query->rows()->current();
	}

	/**
	 * Calculates percentage of required questions user has responded to for
	 * given page
	 *
	 * @return   int
	 */
	public function pageRequiredCompletionPercentage($page)
	{
		$requiredFields = $page->getRequiredFields();
		$requiredCount = $requiredFields->count();
		$responsesCount = $this->_getNonEmptyResponsesTo($requiredFields)
			->count();

		$requiredCompletionPercentage = $this->_calculateCompletionPercentage($requiredCount, $responsesCount);

		return $requiredCompletionPercentage;
	}

	/**
	 * Calculates percentage of required questions user has responded to for
	 * associated form
	 *
	 * @return   int
	 */
	public function requiredCompletionPercentage()
	{
		$requiredFields = $this->_getRequiredFields();
		$requiredCount = $requiredFields->count();
		$responsesCount = $this->_getNonEmptyResponsesTo($requiredFields)
			->count();

		$requiredCompletionPercentage = $this->_calculateCompletionPercentage($requiredCount, $responsesCount);

		return $requiredCompletionPercentage;
	}

	/**
	 * Calculates required completion percentage
	 *
	 * @return   int
	 */
	protected function _calculateCompletionPercentage($requiredCount, $responsesCount)
	{
		if ($requiredCount > 0)
		{
			$requiredCompletionPercentage = round(($responsesCount / $requiredCount) * 100);
		}
		elseif ($this->isNew())
		{
			$requiredCompletionPercentage = 0;
		}
		else
		{
			$requiredCompletionPercentage = 100;
		}

		return $requiredCompletionPercentage;
	}

	/**
	 * Gets forms required fields
	 *
	 * @return   object
	 */
	protected function _getRequiredFields()
	{
		$form = $this->getForm();

		$fields = $form->getRequiredFields();

		return $fields;
	}

	/**
	 * Gets associated form's ID
	 *
	 * @return   object
	 */
	public function getFormId()
	{
		return $this->getForm()->get('id');
	}

	/**
	 * Gets associated form
	 *
	 * @return   object
	 */
	public function getForm()
	{
		$formClass = self::$_formClass;

		$form = $this->belongsToOne($formClass, 'form_id')
			->rows();

		return $form;
	}

	/**
	 * Returns user's non-empty responses to given fields
	 *
	 * @param    object   $fields   Fields to search for responses to
	 * @return   object
	 */
	protected function _getNonEmptyResponsesTo($fields)
	{
		$nonEmptyResponses = $this->_getResponsesTo($fields)
		->where('response', '!=', "");

		return $nonEmptyResponses;
	}

	/**
	 * Returns user's responses to given fields
	 *
	 * @param    object   $fields   Fields to search for responses to
	 * @return   object
	 */
	protected function _getResponsesTo($fields)
	{
		$fieldsIds = $this->_relationalHelper->flatMap($fields, 'id');

		$specificResponses = $this->getResponses()
			->whereIn('field_id', $fieldsIds);

		return $specificResponses;
	}

	/**
	 * Gets users responses for fields associated with a form
	 *
	 * @return   object
	 */
	public function getResponses()
	{
		$fieldsResponseClass = self::$_fieldResponseClass;
		$foreignKey = 'form_response_id';

		$fieldsResponses = $this->oneToMany($fieldsResponseClass, $foreignKey);

		return $fieldsResponses;
	}

	/**
	 * Returns associated user
	 *
	 * @return   object
	 */
	public function getUser()
	{
		$userId = $this->get('user_id');

		return User::one($userId);
	}

	/**
	 * Returns user who reviewed response
	 *
	 * @return   object
	 */
	public function getReviewer()
	{
		$reviewerId = $this->get('reviewed_by');

		return User::oneOrNew($reviewerId);
	}

	/**
	 * Returns string containing response's tag's unaltered names
	 *
	 * @return   string
	 */
	public function getTagString()
	{
		$rawTags = $this->_getRawTags();

		$tagString = join($rawTags, ',');

		return $tagString;
	}

	/**
	 * Returns response's tags unaltered name
	 *
	 * @return   array
	 */
	protected function _getRawTags()
	{
		$tagsData = $this->_getTagsData();

		$rawTags = array_map(function($tagData) {
			return $tagData['raw_tag'];
		}, $tagsData);

		return $rawTags;
	}

	/**
	 * Returns response's tag's data
	 *
	 * @return   array
	 */
	protected function _getTagsData()
	{
		$tagsData = $this->getTags()->rows()->toArray();

		return $tagsData;
	}

	/**
	 * Gets associated tags
	 *
	 * @return   object
	 */
	public function getTags()
	{
		$tagClass = self::$_tagClass;
		$associativeTable = '#__tags_object';
		$primaryKey = 'objectid';
		$foreignKey = 'tagid';

		$tagsAssociations = $this->manyToMany(
			$tagClass,
			$associativeTable,
			$primaryKey,
			$foreignKey
		);
		$responsesTags = $tagsAssociations->whereEquals(
			'#__tags_object.tbl',
			'forms_form_responses'
		);

		return $responsesTags;
	}

	/**
	 * Returns all responses for user w/ given ID
	 *
	 * @param    int      $userId   User record's ID
	 * @return   object
	 */
	public static function allForUser($userId)
	{
		return self::all()
			->whereEquals('user_id', $userId);
	}

}
