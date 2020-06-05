<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Admin\Helpers;

/**
 * HTML helper
 */
class Html
{
	/**
	 * Outputs a <select> element with a specific value chosen
	 *
	 * @param   mixed   $val   Chosen value
	 * @param   string  $name  Field name
	 * @param   string  $id    ID
	 * @param   string  $atts  Attributes
	 * @return  string  HTML <select>
	 */
	public static function scopes($val, $name, $id = null, $atts = null)
	{
		$adapters = \Filesystem::files(dirname(dirname(__DIR__)) . '/models/adapters', '\.php$');

		$out  = '<select name="' . $name . '" id="' . ($id ? $id : str_replace(array('[', ']'), '', $name)) . '"' . ($atts ? ' ' . $atts : '') . '>';
		$out .= '<option value="">' . \Lang::txt('COM_BLOG_SELECT_SCOPE') . '</option>';
		foreach ($adapters as $adapter)
		{
			$adapter = ltrim($adapter, DS);
			$adapter = preg_replace('#\.[^.]*$#', '', $adapter);

			if ($adapter == 'base')
			{
				continue;
			}

			$selected = ($adapter == $val)
					  ? ' selected="selected"'
					  : '';

			$out .= '<option value="' . $adapter . '"' . $selected . '>' . $adapter . '</option>';
		}
		$out .= '</select>';

		return $out;
	}
}
