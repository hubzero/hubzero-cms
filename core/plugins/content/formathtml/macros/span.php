<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * macro class that will wrap some content in a <span> tag
 */
class Span extends Macro
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
