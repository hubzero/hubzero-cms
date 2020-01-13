<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Models;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/prerequisiteScopeClassMap.php";

use Components\Forms\Helpers\PrerequisiteScopeClassMap;
use Hubzero\Database\Relational;

class FormPrerequisite extends Relational
{

	static $FORM_MODEL_NAME = 'Components\Forms\Models\Form';
	static $FORM_RESPONSE_MODEL_NAME = 'Components\Forms\Models\FormResponse';

	protected $table = '#__forms_form_prerequisites';

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
		'prerequisite_id' => 'notempty',
		'prerequisite_scope' => 'notempty',
		'order' => 'notempty'
	];

	/**
	 * Constructs FormPrerequisite instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_scopeMap = new PrerequisiteScopeClassMap();
		parent::__construct();
	}

	/**
	 * Indicates if given user response was accepted
	 *
	 * @param    int    $userId   User's ID
	 * @return   bool
	 */
	public function acceptedFor($userId)
	{
		$this->_setPrereq();

		return $this->_prereq->acceptedFor($userId);
	}

	/**
	 * Returns associated form
	 *
	 * @return   object
	 */
	public function getForm()
	{
		$formModelName = self::$FORM_MODEL_NAME;
		$foreignKey = 'form_id';

		$form = $this->belongsToOne($formModelName, $foreignKey)->row();

		return $form;
	}

	/**
	 * Returns response for user w/ given ID
	 *
	 * @param    int      $userId   User's ID
	 * @return   object
	 */
	public function getResponse($userId)
	{
		$responseModelName = self::$FORM_RESPONSE_MODEL_NAME;
		$responses = $responseModelName::all();
		$prerequisiteId = $this->get('prerequisite_id');

		$response = $responses
			->whereEquals('form_id', $prerequisiteId)
			->whereEquals('user_id', $userId)
			->row();

		return $response;
	}

	/**
	 * Sets prerequisite model
	 *
	 * @return   void
	 */
	protected function _setPrereq()
	{
		if (!isset($this->_prereq))
		{
			$prereqClass = $this->_getPrereqClass();

			$prereqId = $this->get('prerequisite_id');

			$this->_prereq= $prereqClass::oneOrNew($prereqId);
		}
	}

	/**
	 * Maps scope to prerequisite's class
	 *
	 * @return   string
	 */
	protected function _getPrereqClass()
	{
		$scope = $this->get('prerequisite_scope');

		$prereqClass = $this->_scopeMap->getClass($scope);

		return $prereqClass;
	}

	/**
	 * Gets attributes from actual prereq
	 *
	 * @param    string  $key      Attribute to get
	 * @param    mixed   $default  Value to return if key non-existent
	 * @return   mixed
	 */
	public function getParent($key, $default = null)
	{
		$this->_setPrereq();

		return $this->_prereq->get($key, $default);
	}

}
