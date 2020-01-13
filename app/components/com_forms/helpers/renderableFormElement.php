<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class RenderableFormElement
{

	protected static $_metaInputName = 'responses';

	/**
	 * Returns RenderableFormElement instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public  function __construct($args = [])
	{
		$this->_element = $args['element'];
		$this->_respondentId = $args['respondent_id'];
	}

	/**
	 * Proxy get function to form certain attributes
	 *
	 * @param    string   $key       Attribute name
	 * @param    mixed    $default   Default return value
	 * @return   mixed
	 */
	public function get($key, $default = null)
	{
		switch ($key)
		{
			case 'name':
				$value = $this->_getInputName();
				$value = $this->_sanitize($value);
				break;
			case 'default_value':
			case 'max':
			case 'max_length':
			case 'min':
			case 'rows':
			case 'step':
				$value = $this->_element->get($key, $default);
				$value = $this->_sanitize($value);
				break;
			default:
				$value = $this->_element->get($key, $default);
		}

		return $value;
	}

	/**
	 * Returns input value based on given respondent
	 *
	 * @return   mixed
	 */
	public function getInputValue()
	{
		return $this->_element->getInputValue($this->_respondentId);
	}

	/**
	 * Builds input name for form element
	 *
	 * @return   string
	 */
	protected function _getInputName()
	{
		$elementId = $this->_element->get('id');
		$metaInputName = self::$_metaInputName;

		$inputName = $metaInputName . "[$elementId]";

		return $inputName;
	}

	/**
	 * Sanitizes for rendering
	 *
	 * @param    mixed    $value   Attribute value
	 * @return   string
	 */
	protected function _sanitize($value)
	{
		return htmlspecialchars($value, ENT_COMPAT);
	}

	/**
	 * Forward calls to $this->_element
	 *
	 * @param    string   $name   Function name
	 * @param    array    $args   Function arguments
	 * @return   mixed
	 */
	public function __call($name, $args)
	{
		return $this->_element->$name(...$args);
	}

}
