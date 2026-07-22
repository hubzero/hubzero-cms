<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a table of contents for a page
 */
class TableOfContentsMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Outputs a Table of Contents based off the page headers';
		$txt['html'] = '<p>Outputs a Table of Contents based off the page headers</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * The real table of contents is substituted later, during the parser's
	 * heading pass. Here we only emit a placeholder that preserves any
	 * positional arguments (mode/depth) so that pass can honor them.
	 *
	 * @return     string
	 */
	public function render()
	{
		$args = '';
		if ($this->args !== null && $this->args !== '')
		{
			$args = '(' . $this->args . ')';
		}

		return 'MACRO' . $this->uniqPrefix . '[[TableOfContents' . $args . ']]';
	}
}
