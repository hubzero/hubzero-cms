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
 * Wiki macro class for listing children of a page
 */
class ChildrenMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all sub-pages (children) of the current page into the output. Accepts two parameters:
 * \'\'\'depth: how deep to mine for pages. Default is one level.
 * \'\'\'description: show/hide the first line of text from the page.';
		$txt['html'] = '<p>Inserts an alphabetic list of all sub-pages (children) of the current page into the output. Accepts one parameter:</p>
		<ul>
			<li><strong>depth</strong>: how deep to mine for pages. Default is one level.</li>
			<!-- <li><strong>description</strong>: show/hide the first line of text from the page</li> -->
		</ul>
		<p>Example usage: <code>[[Children(depth=3)]]</code></p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$depth = 1;
		$description = 0;

		if ($this->args)
		{
			$args = explode(',', $this->args);
			if (is_array($args))
			{
				foreach ($args as $arg)
				{
					$arg = trim($arg);
					if (substr($arg, 0, 6) == 'depth=')
					{
						$bits = preg_split('#=#', $arg);
						$depth = intval(trim(end($bits)));
						continue;
					}
					if (substr($arg, 0, 12) == 'description=')
					{
						$bits = preg_split('#=#', $arg);
						$description = intval(trim(end($bits)));
						continue;
					}
				}
			}
			else
			{
				$arg = trim($args);
				if (substr($arg, 0, 6) == 'depth=')
				{
					$bits = preg_split('#=#', $arg);
					$depth = intval(trim(end($bits)));
				}
				if (substr($arg, 0, 12) == 'description=')
				{
					$bits = preg_split('#=#', $arg);
					$description = intval(trim(end($bits)));
				}
			}
		}

		$scope = ($this->scope) ? $this->scope . DS . $this->pagename : $this->pagename;

		return $this->listChildren(1, $depth, $scope);
	}

	/**
	 * List children of a page
	 *
	 * @param      integer $currentDepth How far down the tree we are
	 * @param      integer $targetDepth  How far down the tree to go
	 * @param      string  $scope        Page scope
	 * @return     string HMTL
	 */
	private function listChildren($currentDepth, $targetDepth, $scope='')
	{
		$html = '';

		if ($currentDepth > $targetDepth)
		{
			return $html;
		}

		$rows = $this->getchildren($scope);

		if ($rows)
		{
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$title = ($row->title) ? $row->title : $row->pagename;

				$url  = 'index.php?option=' . $this->option;
				$url .= '&scope=' . $row->scope;
				$url .= '&pagename=' . $row->pagename;

				/*$html .= ' * ['.$url;
				$html .= ($row->title) ? ' '.stripslashes($row->title) : ' '.$row->pagename;
				$html .= ']'."\n";*/
				$html .= '<li><a href="' . $url . '">';
				$html .= ($row->title) ? stripslashes($row->title) : $row->pagename;
				$html .= '</a>';
				$html .= $this->listChildren($currentDepth + 1, $targetDepth, $row->scope . DS . $row->pagename);
				$html .= '</li>'."\n";
			}
			$html .= '</ul>';
		}
		elseif ($currentDepth == 1)
		{
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '<p>(No sub-pages to display)</p>';
		}

		return $html;
	}

	/**
	 * Get the children of a page
	 *
	 * @param      string $scope Page scope
	 * @return     array
	 */
	private function getChildren($scope)
	{
		// Get all pages
		$sql = "SELECT * FROM `#__wiki_page` WHERE `scope`=" . $this->_db->quote($scope) . " AND `group_cn`=" . $this->_db->quote($this->domain) . " ORDER BY pagename ASC";

		// Perform query
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}

