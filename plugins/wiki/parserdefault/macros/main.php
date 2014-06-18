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
 * Wiki macro class for displaying a message with links to articles
 */
class MainMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Displays a message containing links to articles with further details on a topic. Accepts a list of comma-separated page names.';
		$txt['html'] = '<p>Displays a message containing links to articles with further details on a topic. Accepts a list of comma-separated page names.</p>';
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

		$pages = explode(',', $et);

		$html = '<div class="rellink relarticle mainarticle">Main articles: ';

		foreach ($pages as $page)
		{
			$page = trim($page);

			// Is it numeric?
			$scope = '';
			if (is_numeric($page))
			{
				// Yes
				$page = intval($page);
			}
			else
			{
				$page = trim($page, DS);
				if (strstr($page, '/') && !strstr($page, ' '))
				{
					$bits = explode('/', $page);
					$page = array_pop($bits);
					$scope = implode('/', $bits);
				}
			}

			if ($this->domain != '' && $scope == '')
			{
				$scope = $this->scope;
			}
			// No, get resource by alias
			$g = new WikiTablePage($this->_db);

			if (strstr($page, ' '))
			{
				$g->loadByTitle($page, $scope);
			}
			else
			{
				$g->load($page, $scope);
			}
			if (!$g->id)
			{
				$g->pagename = $page;
			}

			// Build and return the link
			if ($g->group_cn != '' && $g->scope != '')
			{
				$link = 'index.php?option=com_groups&scope=' . $g->scope . '&pagename=' . $g->pagename;
			}
			else
			{
				$link = 'index.php?option=com_wiki&scope=' . $g->scope . '&pagename=' . $g->pagename;
			}

			if (!$g->id)
			{
				$l[] = '<a href="' . JRoute::_($link) . '">' . stripslashes($g->getTitle()) . '</a>';
			}
			else
			{
				$l[] = '<a class="int-link" href="' . JRoute::_($link) . '">' . stripslashes($g->getTitle()) . '</a>';
			}
		}

		if (count($l) > 1)
		{
			$last = array_pop($l);

			$html .= implode(', ', $l);
			$html .= ' and ' . $last;
		}
		else
		{
			$html .= $l[0];
		}

		return $html . '</div>';
	}
}

