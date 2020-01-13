<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/responseCsvDecorator.php";
require_once "$componentPath/models/form.php";

use Components\Forms\Helpers\ResponseCsvDecorator;
use Components\Forms\Models\Form;
use Hubzero\Utility\Arr;

class ResponsesCsvDecorator implements \Iterator
{

	/**
	 * Constructs ResponsesCsvDecorator instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args)
	{
		$this->_decorator = Arr::getValue(
			$args, 'decorator', new MockProxy(['class' => 'Components\Forms\Helpers\ResponseCsvDecorator'])
		);
		$this->_fields = null;
		$this->_fillableFields = null;
		$this->_form = Arr::getValue($args, 'form', null);
		$this->_formHelper = Arr::getValue(
			$args, 'forms', new MockProxy(['class' => 'Components\Forms\Models\Form'])
		);
		$this->_position = 0;
		$this->_responses = $args['responses'];
	}

	/**
	 * Returns column names
	 *
	 * @return   array
	 */
	public function getColumns()
	{
		$metadata = ['user_id', 'user_name', 'modified'];
		$fieldNames = $this->_getFillableFieldNames();

		$columns = array_merge($metadata, $fieldNames);

		return $columns;
	}

	/**
	 * Retrieves names of form's fields
	 *
	 * @return   array
	 */
	protected function _getFillableFieldNames()
	{
		$form = $this->_getForm();

		$fieldNames = $this->_mapFillableFields('name');

		return $fieldNames;
	}

	/**
	 * Returns resposnes parent form
	 *
	 * @return   object
	 */
	protected function _getForm()
	{
		if (!$this->_form)
		{
			$this->_setForm();
		}

		return $this->_form;
	}

	/**
	 * Sets form
	 *
	 * @return   void
	 */
	protected function _setForm()
	{
		$formId = $this->_getFormId();

		$this->_form = $this->_formHelper->oneOrNew($formId);
	}

	/**
	 * Retrieves form ID
	 *
	 * @return   int
	 */
	protected function _getFormId()
	{
		$formId = 0;
		$responsesCopy = $this->_getResponses();

		if ($responsesCopy->count() > 0)
		{
			$formId = $responsesCopy->first()->get('form_id');
		}

		return $formId;
	}

	/**
	 * Returns copy of responses
	 *
	 * @return   object
	 */
	protected function _getResponses()
	{
		return clone $this->_responses;
	}

	/**
	 * Returns response at given index
	 *
	 * @return   object
	 */
	public function current()
	{
		return $this->_getDecoratedResponse($this->_position);
	}

	/**
	 * Returns response at given index
	 *
	 * @param    int   $index   Numerical index
	 * @return   object
	 */
	protected function _getDecoratedResponse($index)
	{
		$order = $this->_getOrder();
		$response = $this->_getResponse($index);

		return $this->_decorator->create([
			'order' => $order, 'response' => $response
		]);
	}

	/**
	 * Returns field order
	 *
	 * @return   array
	 */
	protected function _getOrder()
	{
		return $this->_mapFillableFields('id');
	}

	/**
	 * Returns given attributes data for form's fields
	 *
	 * @param    string    $attribute   Attribute to map fields by
	 * @return   array
	 */
	protected function _mapFillableFields($attribute)
	{
		return array_map(function($field) use($attribute) {
			return $field->get($attribute);
		}, $this->_getFillableFields());
	}

	/**
	 * Gets all fields that user can fill
	 *
	 * @return   array
	 */
	protected function _getFillableFields()
	{
		if (!$this->_fillableFields)
		{
			$this->_fillableFields = array_filter($this->_getFields(), function($field) {
				return $field->isFillable();
			});
		}

		return $this->_fillableFields;
	}

	/**
	 * Gets form's fields
	 *
	 * @return   array
	 */
	protected function _getFields()
	{
		if (!$this->_fields)
		{
			$this->_fields = $this->_getForm()->getFieldsOrdered();
		}

		return $this->_fields;
	}

	/**
	 * Returns response at given index
	 *
	 * @param    int   $index   Numerical index
	 * @return   object
	 */
	protected function _getResponse($index)
	{
		$responses = $this->_getResponsesArray();

		return $responses[$index];
	}

	/**
	 * Returns array of responses
	 *
	 * @return   array
	 */
	protected function _getResponsesArray()
	{
		return array_merge([], $this->_getResponses()->raw());
	}

	/**
	 * Returns iteration position
	 *
	 * @return   int
	 */
	public function key()
	{
		return $this->_position;
	}

	/**
	 * Returns next iteration position
	 *
	 * @return   int
	 */
	public function next()
	{
		return ++$this->_position;
	}

	/**
	 * Indicates if iteration position is valid
	 *
	 * @return   bool
	 */
	public function valid()
	{
		$i = $this->_position;
		$responsesArray = $this->_getResponsesArray();

		return isset($responsesArray[$i]);
	}

	/**
	 * Resets iteration position
	 *
	 * @return   void
	 */
	public function rewind()
	{
		$this->_position = 0;
	}

}
