<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for getting a linked title to a wiki page
 */
class PageMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro will insert a linked title to a wiki page. It can be passed either an ID or alias.';
		$txt['html'] = '<p>This macro will insert a linked title to a wiki page. It can be passed either an ID or alias.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$p = explode(',', $et);
		$page = array_shift($p);

		$nolink = false;
		$p = explode(' ', end($p));
		foreach ($p as $a)
		{
			$a = trim($a);

			if ($a == 'nolink')
			{
				$nolink = true;
			}
		}

		// Is it numeric?
		$scope = '';
		if (is_numeric($page))
		{
			// Yes
			$row = \Components\Wiki\Models\Page::oneOrNew(intval($page));
		}
		else
		{
			$page = rtrim($page, '/');

			$row = \Components\Wiki\Models\Page::oneByPath($page, $this->domain, $this->domain_id);
		}

		if (!$row->exists())
		{
			return '(Page(' . $et . ') failed)';
		}

		if ($nolink)
		{
			return stripslashes($row->get('title', $row->get('pagename')));
		}

		// Build and return the link
		return '<a href="' . Route::url($row->link()) . '">' . stripslashes($row->get('title', $row->get('pagename'))) . '</a>';
	}
}
