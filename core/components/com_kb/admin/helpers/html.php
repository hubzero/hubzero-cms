<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Admin\Helpers;

/**
 * Knowledge Base helper class for HTML
 */
class Html
{
	/**
	 * Outputs a <select> element with a specific value chosen
	 *
	 * @param   array   $categories  Data to populate list with
	 * @param   mixed   $value       Chosen value
	 * @param   string  $name        Field name
	 * @return  string  HTML <select>
	 */
	public static function categories($categories, $val, $name, $id = null, $atts = null)
	{
		$out  = '<select name="' . $name . '" id="' . ($id ? $id : str_replace(array('[', ']'), '', $name)) . '"' . ($atts ? ' ' . $atts : '') . '>';
		$out .= '<option value="">' . \Lang::txt('COM_KB_SELECT_CATEGORY') . '</option>';
		foreach ($categories as $category)
		{
			$selected = ($category->get('id') == $val)
					  ? ' selected="selected"'
					  : '';
			$prfx = '';

			if ($category->get('level') > 1)
			{
				for ($i = 1; $i <= $category->get('level'); $i++)
				{
					$prfx .= '- ';
				}
			}

			$out .= '<option value="' . $category->get('id') . '"' . $selected . '>' . $prfx . stripslashes($category->get('title')) . '</option>';
		}
		$out .= '</select>';
		return $out;
	}
}
