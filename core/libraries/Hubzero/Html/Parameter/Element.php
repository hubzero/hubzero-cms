<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter;

use Hubzero\Base\Obj;

/**
 * Parameter base class
 *
 * The Element is the base class for all Element types
 */
class Element extends Obj
{
	/**
	 * Element name
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var  object
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   string  $parent  Element parent
	 * @return  void
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get the element name
	 *
	 * @return  string  type of the parameter
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Method to render an xml element
	 *
	 * @param   string  &$xmlElement   Name of the element
	 * @param   string  $value         Value of the element
	 * @param   string  $control_name  Name of the control
	 * @return  array   Attributes of an element
	 */
	public function render(&$xmlElement, $value, $control_name = 'params')
	{
		$name  = (string) $xmlElement['name'];
		$label = (string) $xmlElement['label'];
		$descr = (string) $xmlElement['description'];

		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[0] = $this->fetchTooltip($label, $descr, $xmlElement, $control_name, $name);
		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;

		return $result;
	}

	/**
	 * Method to get a tool tip from an XML element
	 *
	 * @param   string  $label         Label attribute for the element
	 * @param   string  $description   Description attribute for the element
	 * @param   object  &$xmlElement   The element object
	 * @param   string  $control_name  Control name
	 * @param   string  $name          Name attribut
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		$output = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . \App::get('language')->txt($label) . '::' . \App::get('language')->txt($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= \App::get('language')->txt($label) . '</label>';

		return $output;
	}

	/**
	 * Fetch an element
	 *
	 * @param   string  $name          Name attribute of the element
	 * @param   string  $value         Value attribute of the element
	 * @param   object  &$xmlElement   Element object
	 * @param   string  $control_name  Control name of the element
	 * @return  void
	 */
	public function fetchElement($name, $value, &$xmlElement, $control_name)
	{
	}
}
