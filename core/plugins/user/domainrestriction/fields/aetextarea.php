<?php
/**
 * @copyright   Copyright (C) 2005 - 2013 MIchael Richey. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Adjustment to the textarea field which re-implements the translate_default attribute
 */
class JFormFieldAETextarea extends JFormFieldTextarea
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'AETextarea';

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		if ($return) {
			if (version_compare(JVERSION, 3, '>='))
			{
				//error_log('j3');
				$td = ($this->getAttribute('translate_default') == 'true')?true:false;
				$default = ($this->value == $this->default)?$this->default:false;
			}
			else
			{
				//error_log('j2');
				$td = ($element->attributes()->translate_default == 'true')?true:false;
				$default = ($this->value == $element->attributes()->default)?$element->attributes()->default:false;
			}
			if ($td && $default)
			{
				$this->value = Lang::txt($default);
			}
		}
		return $return;
	}
}
