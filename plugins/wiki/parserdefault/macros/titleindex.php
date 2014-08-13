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
 * Wiki macro class for displaying a list of pages
 */
class TitleIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.</p><p>The list may have a sorting applied by adding the sort=[title,created(oldest to newest),modified(newest to oldest)] argument. For example, <code>[[TitleIndex(sort=modified)]]</code> will list all pages by their last modified date (most recent to oldest). If you have a page prefix, simply add a comma and the sort parameter <em>after</em>. For example: <code>[[TitleIndex(Help, sort=modified)]]</code></p>';
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

		$sort = '';
		if ($et) 
		{
			$et = strip_tags($et);

			if (strstr($et, ',')) 
			{
				$attribs = explode(',', $et);
				$et = trim($attribs[0]);
				$sort = strtolower(trim($attribs[1]));
			}

			if (strtolower($et) == 'sort=modified' 
			 || strtolower($et) == 'sort=created' 
			 || strtolower($et) == 'sort=title') 
			{
				$sort = $et;
				$et = '';
			}
		}

		// What pages are we getting?
		switch ($sort)
		{
			case 'sort=modified':
				$sql = "SELECT p.`id`, p.`pagename`, p.`scope`, p.`group_cn`, (CASE WHEN (p.`title` IS NOT NULL AND p.`title` !='') THEN p.`title` ELSE p.`pagename` END) AS `title`, MAX(v.`created`) AS `modified`, MAX(v.`version`) FROM #__wiki_page AS p, #__wiki_version AS v WHERE v.pageid=p.id AND v.approved=1 AND p.state < 2 AND ";
			break;
			case 'sort=created':
			case 'sort=title':
			default:
				$sql = "SELECT p.`id`, p.`pagename`, p.`scope`, p.`group_cn`, (CASE WHEN (p.`title` IS NOT NULL AND p.`title` !='') THEN p.`title` ELSE p.`pagename` END) AS `title`, v.`created`, MAX(v.`version`) FROM #__wiki_page AS p, #__wiki_version AS v WHERE v.pageid=p.id AND v.approved=1 AND p.state < 2 AND ";
			break;
		}

		if ($et) 
		{
			// Get pages with a prefix
			if ($this->domain && substr(strtolower($et), 0, 4) != 'help') 
			{
				$sql .= "LOWER(p.pagename) LIKE '" . strtolower($et) . "%' AND p.`group_cn`=" . $this->_db->quote($this->domain);
			} 
			else 
			{
				$sql .= "LOWER(p.pagename) LIKE '" . strtolower($et) . "%' AND (p.`group_cn`='' OR p.`group_cn` IS NULL)";
			}
		} 
		else 
		{
			// Get all pages
			if ($this->domain) 
			{
				$sql .= "p.`group_cn`=" . $this->_db->quote($this->domain);
			} 
			else 
			{
				$sql .= "(p.`group_cn`='' OR p.`group_cn` IS NULL)";
			}
		}
		switch ($sort)
		{
			case 'sort=created':
				$sql .= " GROUP BY v.pageid ORDER BY `created` ASC";
			break;
			case 'sort=modified':
				$sql .= " GROUP BY v.pageid ORDER BY `modified` DESC";
			break;
			case 'sort=title':
			default:
				$sql .= " GROUP BY v.pageid ORDER BY `title` ASC, `pagename` ASC";
			break;
		}

		// Perform query
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		// Did we get a result from the database?
		if ($rows) 
		{
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				if ($row->pagename == $this->pagename) 
				{
					continue;
				}
				$title = ($row->title) ? $row->title : $row->pagename;

				if (substr($row->pagename, 0, strlen('Help:')) == 'Help:')
				{
					$row->scope = $this->scope;
				}

				$url  = 'index.php?option=' . $this->option;
				$url .= '&scope=' . $row->scope;
				$url .= '&pagename=' . $row->pagename;

				$html .= '<li><a href="' . JRoute::_($url) . '">';
				$html .= ($row->title) ? stripslashes($row->title) : $row->pagename;
				$html .= '</a></li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		} 
		else 
		{
			// Return error message
			return '(No ' . $et . ' pages to display)';
		}
	}
}

