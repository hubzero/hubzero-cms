<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
				$key = strtolower(trim($a[0]));
				if (!in_array($key, array('class', 'id', 'style', 'title', 'lang', 'dir', 'align', 'width', 'height', 'role', 'name', 'tabindex'), true)
					&& !preg_match('/^(data|aria)-[a-z0-9\-]+$/', $key))
				{
					continue;
				}
				$val = trim(end($a), "'\"");

				$atts[] = $key . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
			}
		}

		$span  = '<span';
		$span .= (!empty($atts)) ? ' ' . implode(' ', $atts) . '>' : '>';
		// Macro output is never wiki-parsed again, so the text is plain text
		$span .= htmlspecialchars(trim((string) $text), ENT_QUOTES, 'UTF-8') . '</span>';

		return $span;
	}
}
