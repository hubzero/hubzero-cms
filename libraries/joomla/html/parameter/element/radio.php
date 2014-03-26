<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Renders a radio element
 *
 * @package     Joomla.Platform
 * @subpackage  Parameter
 * @since       11.1
 * @deprecated  12.1    Use JFormFieldRadio instead
 */
class JElementRadio extends JElement
{
	/**
	 * Element name
	 *
	 * @var    string
	 */
	protected $_name = 'Radio';

	/**
	 * Fetch a html for a radio button
	 *
	 * @param   string       $name          Element name
	 * @param   string       $value         Element value
	 * @param   JXMLElement  &$node         JXMLElement node object containing the settings for the element
	 * @param   string       $control_name  Control name
	 *
	 * @return  string
	 *
	 * @deprecated    12.1  Use JFormFieldRadio::getInput and JFormFieldRadio::getOptions indsead.
	 * @since   11.1
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		// Deprecation warning.
		JLog::add('JElementRadio::fetchElement() is deprecated.', JLog::WARNING, 'deprecated');

		$options = array();
		foreach ($node->children() as $option)
		{
			$val = $option->attributes('value');
			$text = $option->data();
			$options[] = JHtml::_('select.option', $val, $text);
		}

		return JHtml::_('select.radiolist', $options, '' . $control_name . '[' . $name . ']', 'class="option"', 'value', 'text', $value, $control_name . $name, true) . '</fieldset>';
	}

	/**
	 * Method to get a tool tip from an XML element
	 *
	 * @param   string       $label         Label attribute for the element
	 * @param   string       $description   Description attribute for the element
	 * @param   JXMLElement  &$xmlElement   The element object
	 * @param   string       $control_name  Control name
	 * @param   string       $name          Name attribut
	 *
	 * @return  string
	 *
	 * @deprecated  12.1
	 * @since   11.1
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		// Deprecation warning.
		JLog::add('JElement::fetchTooltip is deprecated.', JLog::WARNING, 'deprecated');

		$output = '<fieldset id="' . $control_name . $name . '-lbl" class="radio" data-for="' . $control_name . $name . '"><legend';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= JText::_($label) . '</legend>';

		return $output;
	}
}
