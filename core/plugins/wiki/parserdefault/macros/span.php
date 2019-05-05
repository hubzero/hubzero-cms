<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class that will wrap some content in a <span> tag
 */
class SpanMacro extends WikiMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Wraps text or other elements inside a `<span>` tag.';
		$txt['html'] = '<p>Wraps text or other elements inside a <code>&lt;span&gt;</code> tag.</p>';
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

		$atts = array();
		if (!empty($attribs) && count($attribs) > 0)
		{
			foreach ($attribs as $a)
			{
				$a = preg_split('/=/', $a);
				$key = $a[0];
				$val = end($a);

				$atts[] = $key . '="' . trim($val, "'\"") . '"';
			}
		}

		$span  = '<span';
		$span .= (!empty($atts)) ? ' ' . implode(' ', $atts) . '>' : '>';
		$span .= trim($text) . '</span>';

		return $span;
	}
}
