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

namespace Components\Groups\Models;

use Components\Groups\Tables;
use Hubzero\Base\Model;
use Hubzero\Base\Model\ItemList;
use Lang;

// include tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'page.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'page.hit.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'page.version.php';

// include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'page' . DS . 'version' . DS . 'archive.php';

/**
 * Group page model class
 */
class Page extends Model
{
	/**
	 * Table
	 *
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Groups\\Tables\\Page';

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	protected $_versions = null;

	/**
	 * Versions Count
	 *
	 * @var integer
	 */
	protected $_versions_count = null;

	/**
	 * Constructor
	 *
	 * @param   mixed $oid Object Id
	 * @return  oid
	 */
	public function __construct($oid = null)
	{
		// create needed objects
		$this->_db = \App::get('db');

		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}

		// load versions
		/*$pageVersionArchive = new Page\Version\Archive();
		$this->_versions = $pageVersionArchive->versions('list', array(
			'pageid'  => $this->get('id', -1),
			'orderby' => 'version DESC',
			'limit'   => 25
		));*/
	}

	/**
	 * Get Page Versions
	 *
	 * @return  object \Hubzero\Base\ItemList
	 */
	public function versions()
	{
		if (!isset($this->_versions))
		{
			$pageVersionArchive = new Page\Version\Archive();
			$this->_versions = $pageVersionArchive->versions('list', array(
				'pageid'  => $this->get('id', -1),
				'orderby' => 'version DESC',
			));
		}
		return $this->_versions;
	}

	/**
	 * Load Page Version
	 *
	 * @param   mixed  $vid Version Id
	 * @return  object \Components\Groups\Models\Page\Version
	 */
	public function version($vid = null)
	{
		// var to hold version
		$version = new Page\Version();

		// make sure we have versions to return
		if ($this->versions()->count() > 0)
		{
			// return version object
			if ($vid == null || $vid == 0 || $vid == 'current')
			{
				$version = $this->versions()->first();
			}
			else if (is_numeric($vid))
			{
				$version = $this->versions()->fetch('version', $vid);
			}
		}

		//return version
		return $version;
	}

	/**
	 * Load Page Category
	 *
	 * @return  object \Components\Groups\Models\Page\Category
	 */
	public function category()
	{
		// var to hold category
		$category = new Page\Category($this->get('category'));

		// return category
		return $category;
	}

	/**
	 * Load Approved Page version
	 *
	 * @return  object \Components\Groups\Models\Page\Version
	 */
	public function approvedVersion()
	{
		return $this->versions()->fetch('approved', 1);
	}

	/**
	 * Check to see if group owns page
	 *
	 * @param   object  $group \Hubzero\User\Group
	 * @return  boolean
	 */
	public function belongsToGroup($group)
	{
		if ($this->get('gidNumber') == $group->get('gidNumber'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate a unique page alias or slug
	 *
	 * @return  string
	 */
	public function uniqueAlias()
	{
		// if we didnt set an alias lets build one from the title
		$alias = trim($this->get('alias'));
		if ($alias == null)
		{
			$alias = str_replace(' ', '_', trim($this->get('title')));
		}

		// force lowercase letters
		$alias = strtolower($alias);

		// allow only alpha numeric chars, dashes, and underscores
		$alias = preg_replace("/[^-_a-z0-9]+/", '', $alias);

		// make sure alias isnt a reserved term
		$group   = \Hubzero\User\Group::getInstance($this->get('gidNumber'));
		$plugins = \Hubzero\User\Group\Helper::getPluginAccess($group);
		$reserved = array_keys($plugins);

		// make sure dont use a reserved alias on the first level
		if (in_array($alias, $reserved)
			&& $this->get('depth') <= 2
			&& $this->get('home') == 0)
		{
			$alias .= '_page';
		}

		// get current page as it exists in db
		$page = new Page( $this->get('id') );
		$currentAlias = $page->get('alias');

		// only against our pages if alias has changed
		if ($currentAlias != $alias)
		{
			// make sure we dont already have a page with the same alias
			// get group pages
			$pageArchive = Page\Archive::getInstance();
			$aliases = $pageArchive->pages('alias', array(
				'gidNumber' => $group->get('gidNumber'),
				'state'     => array(0,1),
				'depth'     => $this->get('depth')
			));

			// Append random number if page already exists
			while (in_array($alias, $aliases))
			{
				$alias .= mt_rand(1, 9);
			}
		}

		// return sanitized alias
		return $alias;
	}

	/**
	 * Get the next page order
	 *
	 * @param   integer $gidNumber Group ID
	 * @return  integer
	 */
	public function getNextOrder($gidNumber)
	{
		$where = "gidNumber=" . $this->_db->quote($gidNumber) . " AND state != 2";
		$order = $this->_tbl->getNextOrder($where);
		return $order;
	}

	/**
	 * Reorder page
	 *
	 * @param   string  $move      Direction and Magnitude
	 * @param   integer $gidNumber Group ID
	 * @return  void
	 */
	public function move($move, $gidNumber)
	{
		// build where statement
		$where = "gidNumber=" . $this->_db->quote($gidNumber);

		// determine if we need to move up or down
		$dir = '';
		if ($move < 0)
		{
			$dir = '-';
			$move = substr($move, 1);
		}

		// move the number of times different
		for ($i=0; $i < $move; $i++)
		{
			$this->_tbl->move($dir . '1', $where);
		}
	}

	/**
	 * Method to build url to page
	 *
	 * @return  string
	 */
	public function url($includeBase = true)
	{
		// loag group
		$group = \Hubzero\User\Group::getInstance($this->get('gidNumber'));

		// base link, jroute
		$pageLink = '';
		if ($includeBase)
		{
			$pageLink = \Route::url('index.php?option=com_groups&cn=' . $group->get('cn'));
		}

		// get our parents
		$parents = $this->getRecursiveParents($this);

		// get array of aliases
		$segments = $parents->lists('alias');
		$segments = array_filter($segments);

		// remove home page
		$search = array_search('overview', $segments);
		if ($search !== false)
		{
			unset($segments[$search]);
		}

		// add our current page
		// if we not linking to the home page
		if (!$this->get('home'))
		{
			$segments[] = $this->get('alias');
		}

		// if we have segments append them
		if (count($segments) > 0)
		{
			$pageLink .= DS . implode($segments, DS);
		}

		// return routed link
		return $pageLink;
	}

	/**
	 * Get Parent Parent
	 *
	 * @return object  \Components\Groups\Models\Page Object
	 */
	public function getParent()
	{
		return new Page($this->get('parent'));
	}

	/**
	 * Get Page Children
	 *
	 * @return
	 */
	public function getChildren()
	{
		// load pages that are decendents of this page
		$archive  = new Page\Archive();
		$children = $archive->pages('list', array(
			'gidNumber' => $this->get('gidNumber'),
			'left'      => $this->get('lft'),
			'right'     => $this->get('rgt'),
			'orderby'   => 'lft ASC'
		));

		return $children;
	}

	/**
	 * Get Parents Recursively
	 *
	 * @param  [type] $page      [description]
	 * @param  string $direction [description]
	 * @return [type]            [description]
	 */
	public function getRecursiveParents($page, $sort = 'ASC')
	{
		// new item list object to store parents
		// this way we have access to all page vars
		$parents = new ItemList();

		// starting at current page loop through in
		// reverse order until our parent page doesnt have a parent
		while ($page->get('parent') != 0)
		{
			$page = new Page($page->get('parent'));
			$parents->add($page);
		}

		// return parents
		return ($sort == 'ASC') ? $parents->reverse() : $parents;
	}

	/**
	 * Display indicator of Heirarchy
	 *
	 * @param  [type] $hierarchyIndicator [description]
	 * @return [type]                     [description]
	 */
	public function heirarchyIndicator($hierarchyIndicator = ' &mdash; ')
	{
		$parents = $this->getRecursiveParents($this);
		return str_repeat($hierarchyIndicator, $parents->count());
	}
}
