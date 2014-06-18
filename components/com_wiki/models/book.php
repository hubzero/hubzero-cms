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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'page.php');

/**
 * Courses model class for a page
 */
class WikiModelBook extends \Hubzero\Base\Object
{
	/**
	 * Wiki domain
	 *
	 * @var string
	 */
	private $_scope = '__site__';

	/**
	 * WikiModelIterator
	 *
	 * @var object
	 */
	private $_pages = null;

	/**
	 * WikiModelPage
	 *
	 * @var object
	 */
	private $_page = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = null;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_config = null;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_tbl = null;

	/**
	 * Container for cached data
	 *
	 * @var array
	 */
	private $_cache = array(
		'pages_count' => null,
		'pages'       => null,
		'page'        => null
	);

	/**
	 * Constructor
	 *
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($scope='__site__')
	{
		$this->_db = JFactory::getDBO();

		$this->_scope = $scope;

		$this->_tbl = new WikiTablePage($this->_db);

		if (!defined('WIKI_SUBPAGE_SEPARATOR'))
		{
			define('WIKI_SUBPAGE_SEPARATOR', $this->config('subpage_separator', '/'));
		}
		if (!defined('WIKI_MAX_PAGENAME_LENGTH'))
		{
			define('WIKI_MAX_PAGENAME_LENGTH', $this->config('max_pagename_length', 100));
		}
	}

	/**
	 * Returns a reference to a page model
	 *
	 * This method must be invoked as:
	 *     $offering = ForumModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCourse
	 */
	static function &getInstance($scope='__site__')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = (!$scope) ? '__site__' : $key;

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($scope);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property))
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property}))
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Load a wiki with default content
	 * This is largely Help pages
	 *
	 * @param      string $option Component name
	 * @return     string
	 */
	public function scribe($option)
	{
		$pages = $this->_defaultPages();

		if (count($pages) <= 0)
		{
			return JText::_('No default pages found');
		}

		$p = WikiHelperParser::getInstance();

		foreach ($pages as $f => $c)
		{
			$f = str_replace('_', ':', $f);

			// Instantiate a new page
			$page = new WikiTablePage($this->_db);
			$page->pagename = $f;
			$page->title    = $page->getTitle();
			if ($this->_scope != '__site__')
			{
				$page->group_cn = $this->_scope;
				$page->scope    = $this->_scope . '/wiki';
			}
			if ($this->_scope == '__site__' && $page->pagename == 'MainPage')
			{
				$page->params = 'mode=static' . "\n";
			}
			else
			{
				$page->params = 'mode=wiki' . "\n";
			}

			// Check content
			if (!$page->check())
			{
				JError::raiseWarning(500, $page->getError());
				return;
			}

			// Store content
			if (!$page->store())
			{
				JError::raiseWarning(500, $page->getError());
				return;
			}
			// Ensure we have a page ID
			if (!$page->id)
			{
				$page->id = $this->_db->insertid();
			}

			// Instantiate a new revision
			$revision = new WikiTableRevision($this->_db);
			$revision->pageid     = $page->id;
			$revision->minor_edit = 0;
			$revision->version    = 1;
			$revision->pagetext   = $c;
			$revision->approved   = 1;

			$wikiconfig = array(
				'option'   => $option,
				'scope'    => $page->scope,
				'pagename' => $page->pagename,
				'pageid'   => $page->id,
				'filepath' => '',
				'domain'   => $page->group_cn
			);

			// Transform the wikitext to HTML
			if ($page->pagename != 'Help:WikiMath')
			{
				$revision->pagehtml = $p->parse($revision->pagetext, $wikiconfig, true, true);
			}

			// Check content
			if (!$revision->check())
			{
				JError::raiseWarning(500, $revision->getError());
				return;
			}
			// Store content
			if (!$revision->store())
			{
				JError::raiseWarning(500, $revision->getError());
				return;
			}

			$page->version_id = $revision->id;
			$page->modified   = $revision->created;
			if (!$page->store())
			{
				// This really shouldn't happen.
				JError::raiseWarning(500, $page->getError());
				return;
			}
		}

		return null;
	}

	/**
	 * Get an associative list of default pages and their content
	 *
	 * @return     array
	 */
	private function _defaultPages()
	{
		$path = dirname(dirname(__FILE__)) . DS . 'default';
		if ($this->_scope != '__site__')
		{
			$path .= DS . 'groups';
		}

		$pages = array();

		if (is_dir($path))
		{
			jimport('joomla.filesystem.file');

			$dirIterator = new DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot() || $file->isDir())
				{
					continue;
				}

				if ($file->isFile())
				{
					$fl = $file->getFilename();
					if (strtolower(JFile::getExt($fl)) == 'txt')
					{
						$name = JFile::stripExt($fl);
						$pages[$name] = JFile::read($path . DS . $fl);
					}
				}
			}
		}

		return $pages;
	}

	/**
	 * Get a configuration value
	 *
	 * @param	   string $key Property to return
	 * @return     mixed
	 */
	public function scope($scope=null)
	{
		if ($scope)
		{
			$this->_scope = $scope;
		}
		return $this->_scope;
	}

	/**
	 * Get a configuration value
	 *
	 * @param	   string $key Property to return
	 * @return     mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_wiki');
		}
		if ($key)
		{
			return $this->_config->get($key, $default);
		}
		return $this->_config;
	}

	/**
	 * Set and get a specific offering
	 *
	 * @param      mixed $id Integer or string of tag to look up
	 * @return     object TagsModelTag
	 */
	public function page($id=null)
	{
		if (!isset($this->_cache['page']) && $id === null)
		{
			$pagename = trim(JRequest::getVar('pagename', '', 'default', 'none', 2));
			if (substr(strtolower($pagename), 0, strlen('image:')) != 'image:'
			 && substr(strtolower($pagename), 0, strlen('file:')) != 'file:')
			{
				$pagename = $this->_tbl->normalize($pagename);
			}
			JRequest::setVar('pagename', $pagename);

			$scope = JRequest::getVar('scope', '');
			if ($scope)
			{
				// Clean the scope. Since scope is built of a chain of pagenames or groups/groupname/wiki
				// the wiki normalize() should strip any nasty stuff out
				$bits = explode('/', $scope);
				foreach ($bits as $i => $bit)
				{
					$bits[$i] = $this->_tbl->normalize($bit);
				}
				$scope = implode('/', $bits);
				JRequest::setVar('scope', $scope);
			}

			$task = trim(JRequest::getWord('task', ''));

			// No page name given! Default to the home page
			if (!$pagename && $task != 'new')
			{
				return $this->main();
			}

			// Load the page
			$this->_cache['page'] = new WikiModelPage($pagename, $scope);

			if (!$this->_cache['page']->exists() && $this->_cache['page']->get('namespace') == 'help')
			{
				$this->_cache['page'] = new WikiModelPage($pagename, '');
				$this->_cache['page']->set('scope', $scope);
				if ($this->_scope != '__site__')
				{
					$this->_cache['page']->set('group', $this->_scope);
				}
			}
		}

		if (!isset($this->_cache['page'])
		 || (
				$id !== null
			 && (int) $this->_cache['page']->get('id') != $id
			 && (string) $this->_cache['page']->get('pagename') != $this->_tbl->normalize($id)
			)
		 )
		{
			$this->_cache['page'] = null;

			if (isset($this->_cache['pages']) && is_a($this->_cache['pages'], 'WikiModelIterator'))
			{
				foreach ($this->_cache['pages'] as $page)
				{
					if ((int) $page->get('id') == $id || (string) $page->get('pagename') == $this->_tbl->normalize($id))
					{
						$this->_cache['page'] = $page;
						break;
					}
				}
			}

			if (!$this->_cache['page'])
			{
				$this->_cache['page'] = WikiModelPage::getInstance($id, JRequest::getVar('scope', ''));
			}
		}

		return $this->_cache['page'];
	}

	/**
	 * Set and get a specific offering
	 *
	 * @param      mixed $id Integer or string of tag to look up
	 * @return     object TagsModelTag
	 */
	public function main()
	{
		$this->_cache['page'] = null;

		if (isset($this->_cache['pages']) && is_a($this->_cache['pages'], 'WikiModelIterator'))
		{
			foreach ($this->_cache['pages'] as $page)
			{
				if ($page->get('main', 0) == 1)
				{
					$this->_cache['page'] = $page;
					break;
				}
			}
		}

		if ($this->_cache['page'])
		{
			return $this->_cache['page'];
		}

		return $this->page($this->config('homepage', 'MainPage'));
	}

	/**
	 * Get a list of tags
	 *
	 * @param      string  $rtrn    Format of data to return
	 * @param      array   $filters Filters to apply
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function pages($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['group']) && $this->_scope != '__site__')
		{
			$filters['group'] = $this->_scope;
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array('0', '1');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['pages_count']) || $clear)
				{
					$this->_cache['pages_count'] = (int) $this->_tbl->getPagesCount($filters);
				}
				return $this->_cache['pages_count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['pages']) || !($this->_cache['pages'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $this->_tbl->getPages($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new WikiModelPage($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['pages'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['pages'];
			break;
		}
	}

	/**
	 * Get a revisions for a wiki page
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function templates($what='list', $filters=array(), $clear=false)
	{
		$filters['namespace'] = 'Template';
		$filters['sortby'] = 'title ASC';
		$filters['scope'] = JRequest::getVar('scope', '');

		return $this->pages($what, $filters, $clear);
	}

	/**
	 * Get a list of special pages
	 *
	 * @return     array
	 */
	public function special()
	{
		static $pages;

		if (!isset($pages))
		{
			$path = JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'views' . DS . 'special' . DS . 'tmpl';

			$pages = array();

			if (is_dir($path))
			{
				jimport('joomla.filesystem.file');

				// Loop through all files and separate them into arrays of images, folders, and other
				$dirIterator = new DirectoryIterator($path);
				foreach ($dirIterator as $file)
				{
					if ($file->isDot() || $file->isDir())
					{
						continue;
					}

					if ($file->isFile())
					{
						$name = $file->getFilename();
						if (JFile::getExt($name) != 'php'
						 || 'cvs' == strtolower($name)
						 || '.svn' == strtolower($name))
						{
							continue;
						}

						$pages[] = strtolower(JFile::stripExt($name));
					}
				}

				sort($pages);
			}
		}

		return $pages;
	}

	/**
	 * Get a list of special pages
	 *
	 * @return     array
	 */
	public function groups()
	{
		return $this->_tbl->getGroups();
	}
}

