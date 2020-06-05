<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use App;

/**
 * Renders a textarea element
 */
class Textarea extends Base
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Textarea';

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
		$rows = isset($element->rows) ? $element->rows : 6;
		$cols = isset($element->cols) ? $element->cols : 50;

		$cls = array();
		if (isset($element->class))
		{
			$cls[] = $element->class;
		}
		$cls[] = (App::isAdmin() ? 'no-footer' : 'minimal no-footer');

		// convert tags so they are safe for wysiwyg and source editors
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<span class="field-wrap">' . App::get('editor')->display($control_name . '[' . $name . ']', $value, '', '', $cols, $rows, false, $control_name . '-' . $name, null, null, array('class' => implode(' ', $cls))) . '</span>';
	}
}
