<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying an unreferenced section message
 */
class UnreferencedMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Displays a notice that a section is unreferenced. Accepts a date as the only argument.';
		$txt['html'] = '<p>Displays a notice that a section is unreferenced. Accepts a date as the only argument.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$dt = '';

		if ($this->args)
		{
			$dt .= ' <span class="mbox-date">(' . $this->args . ')</span>';
		}

		return '<div class="mbox-content mbox-unreferenced"><p class="mbox-text">This section <strong>does not cite any references or sources</strong>.' . $dt . '</span></p></div>';
	}
}
