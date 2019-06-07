<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for wrapping content in a div
 */
class DivMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Allows content to be wrapped in a `div` tag. This macro must be used twice: `Div(start)` to indicate where to create the opening `div` tag and `Div(end)` to indicate where to close the resulting `div` tag. Attributes may be applied by separating name/value pairs with a comma. Example: Div(start, class=myclass)';
		$txt['html'] = '<p>Allows content to be wrapped in a <code>&lt;div&gt;</code> tag. This macro must be used twice: <code>[[Div(start)]]</code> to indicate where to create the opening <code>&lt;div&gt;</code> tag and <code>[[Div(end)]]</code> to indicate where to close the resulting <code>&lt;div&gt;</code> tag. Attributes may be applied by separating name/value pairs with a comma. Example: <code>[[Div(start, class=myclass)]]</code>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$attribs = explode(',', $et);
		$text = array_shift($attribs);

		if (trim($text) == 'start')
		{
			$atts = array();
			if (!empty($attribs) && count($attribs) > 0)
			{
				foreach ($attribs as $a)
				{
					$a = explode('=', $a);
					$key = trim($a[0]);
					$val = trim(end($a));
					$val = trim($val, '"');
					$val = trim($val, "'");

					$key = htmlentities($key, ENT_COMPAT, 'UTF-8');
					$val = htmlentities($val, ENT_COMPAT, 'UTF-8');

					$atts[] = $key . '="' . $val . '"';
				}
			}

			$div  = '<div';
			$div .= (!empty($atts)) ? ' ' . implode(' ', $atts) . '>' : '>';
		}
		elseif (trim($text) == 'end')
		{
			$div  = '</div>';
		}

		return $div;
	}
}
