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
 * Wiki macro class for displaying a tree of page parents
 */
class ParentsMacro extends WikiMacro
{
	/**
	 * Returns a description of how to use the macro
	 *
	 * @return string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts a nested list of ancestor pages (parents) of the current page into the output. Accepts one parameter:
 * \'\'\'depth:\'\'\' how deep to mine for pages. Default is one level.';
		$txt['html'] = '<p>Inserts a nested list of ancestor pages (parents) of the current page into the output. Accepts one parameter:</p>
		<ul>
			<li><strong>depth</strong>: how deep to mine for pages. Default is one level.</li>
		</ul>
		<p>Example usage: <code>[[Parents(depth=3)]]</code></p>';
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
				else
				{
					$depth = intval(trim($arg));
					$depth = ($depth) ? $depth : 1;
				}
			}
		}

		// $depth needs to be 1 or more
		if ($depth == 0)
		{
			return '';
		}

		// If no scope, then this is a top-level page (ie, no parents)
		if (!$this->scope)
		{
			return '';
		}

		// Get an array of ancestors
		$rows = $this->_fetchPointer($depth, $this->scope);

		// Check for any results
		if ($rows && is_array($rows))
		{
			// Return nested lists
			return $this->_buildTree($rows);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Build a tree of parents
	 *
	 * @param  array  $rows An array of objects
	 * @return string
	 */
	private function _buildTree($rows)
	{
		$html = '';

		if ($rows && count($rows) > 0)
		{
			// Get the last element in the array
			$row = array_pop($rows);

			$title = ($row->title) ? $row->title : $row->pagename;

			$url  = 'index.php?option=' . $this->option;
			$url .= '&scope=' . $row->scope;
			$url .= '&pagename=' . $row->pagename;

			// Build the HTML
			$html .= '<ul>';
			$html .= '<li><a href="' . JRoute::_($url) . '">';
			$html .= ($row->title) ? stripslashes($row->title) : $row->pagename;
			$html .= '</a>';
			$html .= $this->_buildTree($rows);
			$html .= '</li>' . "\n";
			$html .= '</ul>';
		}

		return $html;
	}

	/**
	 * Build a tree of parents
	 *
	 * @param  integer $depth How far back to look for ancestors
	 * @param  string  $scope The URI path to traverse
	 * @return array
	 */
	private function _fetchPointer($depth, $scope)
	{
		$uri = explode('/', $scope);

		$pages = array();
		if (!is_array($uri))
		{
			return $pages;
		}

		$uri = array_reverse($uri);

		$subscope = null;

		$i = 0;
		foreach ($uri as $uriPart)
		{
			$i++;

			if (!$subscope)
			{
				$subscope = array_reverse($uri);
			}
			array_pop($subscope);

			// fetch the pointer to the current uri part
			$pointer = $this->_getPageByAlias($uriPart, implode('/', $subscope));

			// if the page was not found then return null
			if (null == $pointer)
			{
				return $pages;
			}

			//set the parent id to the current pointer to traverse down the tree
			$pages[] = $pointer;

			if ($i == $depth)
			{
				break;
			}
		}
		return $pages;
	}

	/**
	 * Retrieve a wiki page by alias
	 *
	 * @param  integer $depth How far back to look for ancestors
	 * @param  string  $scope The URI path to traverse
	 * @return array
	 */
	private function _getPageByAlias($alias, $scope)
	{
		if (!class_exists('WikiTablePage') && is_file(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php'))
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
		}

		$page = new WikiTablePage($this->_db);
		$page->load($alias, $scope);

		// Check for a result
		if ($page && $page->id)
		{
			return $page;
		}
		else
		{
			return null;
		}
	}
}
