<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Resource element base class
 */
class ResourcesElement extends JObject
{
	/**
	* Element name
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @var string
	*/
	protected $_name = null;

	/**
	* Reference to the object that instantiated the element
	*
	* @var object
	*/
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	* Get the element name
	*
	* @access public
	* @return string type of the parameter
	*/
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   object  $element       Data Source Object.
	 * @param   string  $value         Selected value for the element
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  object  An object populated with all the data and HTML for an element
	 */
	public function render(&$element, $value, $control_name = 'fields')
	{
		$name	= $element->name;
		$label	= isset($element->label) ? $element->label : $element->name;
		$descr	= isset($element->description) ? $element->description : '';

		// Make sure we have a valid label
		$label = $label ? $label : $name;

		$result = new stdClass;
		$result->label = $this->fetchTooltip($label, $descr, $element, $control_name, $name);
		$result->element = $this->fetchElement($name, $value, $element, $control_name);
		$result->description = $descr;
		$result->text  = $label;
		$result->value = $value;
		$result->name  = $name;
		$result->type  = $element->type;

		return $result;
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $label         Display name of the field
	 * @param   string  $description   Description for the field
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @param   string  $name          Name of the field
	 * @return  string  HTML
	 */
	public function fetchTooltip($label, $description, &$element, $control_name='', $name='')
	{
		$output = '<label id="' . $control_name . '-' . $name . '-lbl" for="' . $control_name . '-' . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= JText::_($label);
		$output .= (isset($element->required) && $element->required) ? ' <span class="required">' . JText::_('JOPTION_REQUIRED') . '</span>' : '';
		$output .= '</label>';

		return $output;
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchElement($name, $value, &$element, $control_name)
	{
		return '';
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchOptions($name, $value, &$element, $control_name)
	{
		return JText::_('COM_RESOURCES_NONE');
	}

	/**
	 * Display a value
	 *
	 * @param   string  $value   Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		return $value;
	}

	/**
	 * Create html tag for element.
	 * 
	 * @param  string $tag    Tag Name
	 * @param  sting  $value  Tag Value
	 * @param  string $prefix Tag prefix
	 * @return string HTML
	 */
	public function toHtmlTag($tag, $value, $prefix = 'nb:')
	{
		return "<{$prefix}{$tag}>{$value}</{$prefix}{$tag}>";
	}
}