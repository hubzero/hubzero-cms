<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a tree of page parents
 */
class ParentsMacro extends WikiMacro
{
	/**
	 * Returns a description of how to use the macro
	 *
	 * @return  string
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
	 * @return  string
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

		return '';
	}

	/**
	 * Build a tree of parents
	 *
	 * @param   array  $rows  An array of objects
	 * @return  string
	 */
	private function _buildTree($rows)
	{
		$html = '';

		if ($rows && count($rows) > 0)
		{
			// Get the last element in the array
			$row = array_pop($rows);

			// Build the HTML
			$html .= '<ul>';
			$html .= '<li><a href="' . Route::url($row->link()) . '">';
			$html .= stripslashes($row->title);
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
	 * @param   integer  $depth  How far back to look for ancestors
	 * @param   string   $scope  The URI path to traverse
	 * @return  array
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
	 * @param   string  $alias
	 * @param   string  $scope
	 * @return  mixed
	 */
	private function _getPageByAlias($alias, $scope)
	{
		$page = \Components\Wiki\Models\Page::oneByPath(($scope ? $scope . '/' . $alias : ''), $this->doman, $this->domain_id);

		// Check for a result
		if ($page->get('id'))
		{
			return $page;
		}

		return null;
	}
}
