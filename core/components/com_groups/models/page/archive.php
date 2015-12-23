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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Page;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Object;
use Hubzero\Base\Model\ItemList;

// include needed models
require_once dirname(__DIR__) . DS . 'page.php';
require_once __DIR__ . DS . 'category' . DS . 'archive.php';

/**
 * Group page archive model class
 */
class Archive extends Object
{
	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_pages = null;

	/**
	 * Page count
	 *
	 * @var integer
	 */
	private $_pages_count = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * \Hubzero\User\Group
	 *
	 * @var object
	 */
	private $_group = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db    = \App::get('db');
		$this->_group = \Hubzero\User\Group::getInstance(\Request::getVar('cn', ''));
	}

	/**
	 * Get Instance of Page Archive
	 *
	 * @param   string $key Instance Key
	 * @return  object \Components\Groups\Models\PageArchive
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group pages
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function pages($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'alias':
				$aliases = array();
				if ($results = $this->pages('list', $filters, true))
				{
					foreach ($results as $result)
					{
						$aliases[] = $result->get('alias');
					}
				}
				return $aliases;
			break;
			case 'unapproved':
				$unapproved = array();
				if ($results = $this->pages('list', $filters, true))
				{
					foreach ($results as $k => $result)
					{
						// get current version
						$version = $result->versions()->first();

						if (!$version)
						{
							continue;
						}

						// if current version is unapproved return it
						if ($version->get('approved') == 0)
						{
							$unapproved[] = $result;
						}
					}
				}
				return new ItemList($unapproved);
			break;
			case 'tree':
				$tree = array();
				if ($results = $this->pages('list', $filters, $clear))
				{
					$tree = $this->_buildTree($results);
				}
				return $tree;
			case 'list':
			default:
				if (!($this->_pages instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Page($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Page($result);
						}
					}
					$this->_pages = new ItemList($results);
				}
				return $this->_pages;
			break;
		}
	}

	/**
	 * Reset all pages with new value for key
	 *
	 * @param      string  $key         Page key to reset
	 * @param      string  $value       New value to set
	 * @param      array   $filters     Filters passed to pages() method
	 * @return     mixed
	 */
	public function reset($key = 'home', $value = 0, $filters = array())
	{
		// get list of pages
		$pages = $this->pages('list', $filters);

		// reset each page
		foreach ($pages as $page)
		{
			$page->set($key, $value);
			$page->store();
		}
	}

	/**
	 * Build Multi Dimensional Tree of Pages
	 * 
	 * @param  [type] $results [description]
	 * @return [type]          [description]
	 */
	private function _buildTree($pages)
	{
		// vars to hold the tree array
		$tmpTree = array();
		$tree    = array();

		// first loop organizes by parent
		foreach ($pages as $page)
		{
			if (!isset($tmpTree[$page->get('parent')]))
			{
				$tmpTree[$page->get('parent')] = array();
			}
			if (!isset($tmpTree[$page->get('id')]))
			{
				$tmpTree[$page->get('id')] = array();
			}

			// add our page to the parent array
			$tmpTree[$page->get('parent')][] = $page;
		}

		// second loop attaches children to parent
		foreach ($pages as $page)
		{
			$children = $tmpTree[$page->get('id')];
			$page->set('children', $children);
			$tree[$page->get('parent')][] = $page;
		}

		// only return base node tree if it exists
		if (count($tree) != 0) 
		{
			return $tree[0];
		}
		return null;
	}
}
