<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Wiki macro class for getting the page title or pagename of a page
 */
class PageName extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Accepts either 'alias' or 'title' as arg. Returns either the alias (default if no args given) or title of the current page.";
		$txt['html'] = "<p>Accepts either 'alias' or 'title' as arg. Returns either the alias (default if no args given) or title of the current page.</p>";
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

		switch (trim($et))
		{
			case 'title':
				$sql = "SELECT title FROM `#__wiki_page` WHERE pagename=" . $this->_db->quote($this->pagename) . "' AND `group_cn`=" . $this->_db->quote($this->domain) . " AND scope=" . $this->_db->quote($this->scope);
				// Perform query
				$this->_db->setQuery($sql);
				return stripslashes($this->_db->loadResult());
			break;

			case 'alias':
			default:
				return $this->pagename;
			break;
		}
	}
}
