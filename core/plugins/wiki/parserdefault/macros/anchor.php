<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for anchors
 */
class AnchorMacro extends WikiMacro
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
		$txt['wiki'] = 'Inserts an anchor into a page.';
		$txt['html'] = '<p>Inserts an anchor into a page.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$name = $this->args;

		// Did we get a result from the database?
		if ($name)
		{
			// Build and return the link
			return '<a name="' . urlencode($name) . '"></a>';
		}
	}
}
