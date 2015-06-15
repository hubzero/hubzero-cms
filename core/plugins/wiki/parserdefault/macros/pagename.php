<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki macro class for getting the page title or pagename of a page
 */
class PageNameMacro extends WikiMacro
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
				$sql = "SELECT title FROM `#__wiki_page` WHERE `pagename`=" . $this->_db->quote($this->pagename) . " AND `group_cn`=" . $this->_db->quote($this->domain) . " AND `scope`=" . $this->_db->quote($this->scope);
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

