<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Arr;

class PageField extends Relational
{

	static protected $_responseClass = 'Components\Forms\Models\FieldResponse';
	static protected $_pageModelName = 'Components\Forms\Models\FormPage';

	static protected $_fillableTypes = [
		'checkbox-group',
		'date',
		'hidden',
		'number',
		'radio-group',
		'select',
		'text',
		'textarea'
	];

	/**
	 * Records table
	 *
	 * @var string
	 */
	protected $table = '#__forms_page_fields';

	/**
	 * Attributes to be populated on record creation
	 *
	 * @var array
	 */
	public $initiate = ['created'];

	/**
	 * Attribute validation
	 *
	 * @var  array
	 */
	public $rules = [
		'page_id' => 'positive',
		'order' => 'positive',
		'type' => 'notempty'
	];

	/**
	 * Constructs PageField instance
	 *
	 * @param    array   $args Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_returnDefault = Arr::getValue($args, 'return_default', true);

		parent::__construct();
	}

	/**
	 * Returns field's options
	 *
	 * @return   array
	 */
	public function getOptions()
	{
		$options = $this->get('values', []);
		$decodedOptions = json_decode($options);

		if (!$this->_returnDefault)
		{
			$decodedOptions = $this->_removeOptionDefaults($decodedOptions);
		}

		return $decodedOptions;
	}

	/**
	 * Prevents options from being selected by default
	 *
	 * @param    array   $options   Field's options
	 * @return   array
	 */
	protected function _removeOptionDefaults($options)
	{
		$updatedOptions = array_map(function($option) {
			$option->selected = false;
			return $option;
		}, $options);

		return $updatedOptions;
	}

	/**
	 * Returns value to populate field with
	 *
	 * @return   string
	 */
	public function getInputValue($userId)
	{
		$inputValue = null;
		$userResponse = $this->getResponse($userId);

		if ($userResponseValue = $userResponse->get('response'))
		{
			$inputValue = $userResponseValue;
		}
		else if ($this->_returnDefault)
		{
			$inputValue = $this->get('default_value');
		}

		return $inputValue;
	}

	/**
	 * Returns field response for current user
	 *
	 * @return   object
	 */
	public function getCurrentUsersResponse()
	{
		$currentUserId = self::_getCurrentUsersId();

		$response = $this->getResponse($currentUserId);

		return $response;
	}

	/**
	 * Returns field response for given user
	 *
	 * @param    int      $userId   User's ID
	 * @return   object
	 */
	public function getResponse($userId)
	{
		$responses = $this->getResponses();

		$response = $responses
			->whereEquals('user_id', $userId)
			->rows()
			->current();

		if (!$response)
		{
			$fieldId = $this->get('id');
			$response = self::_nullResponse([
				'field_id' => $fieldId,
				'user_id' => $userId
			]);
		}

		return $response;
	}

	/**
	 * Returns associated field responses
	 *
	 * @return   object
	 */
	public function getResponses()
	{
		$responseModelClass = self::$_responseClass;
		$foreignKey = 'field_id';

		$responses = $this->oneToMany($responseModelClass, $foreignKey);

		return $responses;
	}

	/**
	 * Returns page's form's ID
	 *
	 * @return   int
	 */
	public function getFormId()
	{
		$page = $this->getPage();

		return $page->getFormId();
	}

	/**
	 * Returns associated page
	 *
	 * @return   object
	 */
	public function getPage()
	{
		$pageModelName = self::$_pageModelName;
		$foreignKey = 'page_id';

		$page = $this->belongsToOne($pageModelName, $foreignKey)
			->row();

		return $page;
	}

	/**
	 * Returns current user's ID
	 *
	 * @return   int
	 */
	protected static function _getCurrentUsersId()
	{
		$currentUsersId = User::get('id');

		return $currentUsersId;
	}

	/**
	 * Instantiates null response object for given field
	 *
	 * @param    object   $state   Instantiation state
	 * @return   object
	 */
	protected static function _nullResponse($state)
	{
		$response = FieldResponse::blank();

		$response->set($state);

		return $response;
	}

	/**
	 * Indicates if the user can provide input for this field
	 *
	 * @return   bool
	 */
	public function isFillable()
	{
		$type = $this->get('type');

		return in_array($type, self::$_fillableTypes);
	}

}
