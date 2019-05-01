<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders a text element
 */
class Text extends Base
{
	/**
  * Element name
  *
  * @var		string
  */
	protected	$_name = 'Single-line text box';

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
		$size = ( isset($element->size) ? 'size="'.$element->size.'"' : '' );
		$class = ( isset($element->class) ? 'class="'.$element->class.'"' : 'class="text_area"' );
		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<span class="field-wrap"><input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.'-'.$name.'" value="'.$value.'" '.$class.' '.$size.' /></span>';
	}
}
