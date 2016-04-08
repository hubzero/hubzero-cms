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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Base\Object;
use Components\Wiki\Helpers\Parser;
use Filesystem;
use Exception;
use Component;
use Request;
use Lang;

require_once(__DIR__ . DS . 'page.php');

/**
 * Wiki model for a book
 */
class Book extends Object
{
	/**
	 * Registry
	 *
	 * @var  object
	 */
	private $config = null;

	/**
	 * Container for cached data
	 *
	 * @var  object
	 */
	private $page = null;

	/**
	 * Constructor
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);
	}

	/**
	 * Returns a reference to a book model
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  object
	 */
	public static function getInstance($scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = $scope . $scope_id;

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Load a wiki with default content
	 * This is largely Help pages
	 *
	 * @param   string  $option  Component name
	 * @return  string
	 */
	public function scribe($option)
	{
		$pages = $this->_defaultPages();

		if (count($pages) <= 0)
		{
			return Lang::txt('No default pages found');
		}

		$p = Parser::getInstance();

		foreach ($pages as $f => $c)
		{
			$f = str_replace('_', ':', $f);

			// Instantiate a new page
			$page = Page::blank();
			$page->set('pagename', $f);
			$page->set('title', $page->title);
			$page->set('access', 0);
			$page->set('scope', $this->get('scope'));
			$page->set('scope_id', $this->get('scope_id'));
			if ($this->get('scope') != 'site')
			{
				$page->set('path', $this->get('scope') . '/wiki');
			}
			if ($this->get('scope') == 'site' && $page->get('pagename') == 'MainPage')
			{
				$page->set('params', 'mode=static' . "\n");
			}
			else
			{
				$page->set('params', 'mode=wiki' . "\n");
			}

			// Store content
			if (!$page->save())
			{
				throw new Exception($page->getError(), 500);
			}

			// Instantiate a new revision
			$revision = Version::blank();
			$revision->set('pageid', $page->get('id'));
			$revision->set('minor_edit', 0);
			$revision->set('version', 1);
			$revision->set('pagetext', $c);
			$revision->set('approved', 1);

			$wikiconfig = array(
				'option'    => $option,
				'scope'     => $page->get('path'),
				'pagename'  => $page->get('pagename'),
				'pageid'    => $page->get('id'),
				'filepath'  => '',
				'domain'    => $page->get('scope'),
				'domain_id' => $page->get('scope_id')
			);

			// Transform the wikitext to HTML
			if ($page->get('pagename') != 'Help:WikiMath')
			{
				$revision->set('pagehtml', $p->parse($revision->get('pagetext'), $wikiconfig, true, true));
			}

			// Store content
			if (!$revision->save())
			{
				throw new Exception($revision->getError(), 500);
			}

			$page->set('version_id', $revision->get('id'));
			$page->set('modified', $revision->get('created'));

			if (!$page->save())
			{
				// This really shouldn't happen.
				throw new Exception($page->getError(), 500);
			}
		}

		return null;
	}

	/**
	 * Get an associative list of default pages and their content
	 *
	 * @return  array
	 */
	private function _defaultPages()
	{
		$path = dirname(__DIR__) . DS . 'default';

		if ($this->get('scope') != 'site')
		{
			$path .= DS . $this->get('scope');
		}

		$pages = array();

		if (is_dir($path))
		{
			$dirIterator = new \DirectoryIterator($path);

			foreach ($dirIterator as $file)
			{
				if ($file->isDot() || $file->isDir())
				{
					continue;
				}

				if ($file->isFile())
				{
					$fl = $file->getFilename();

					if (strtolower(Filesystem::extension($fl)) == 'txt')
					{
						$name = Filesystem::name($fl);
						$pages[$name] = Filesystem::read($path . DS . $fl);
					}
				}
			}
		}

		return $pages;
	}

	/**
	 * Get a configuration value
	 *
	 * @param   string  $key      Property to return
	 * @param   mixed   $default  Value to return if property not found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = Component::params('com_wiki');
		}

		if ($key)
		{
			return $this->config->get($key, $default);
		}

		return $this->config;
	}

	/**
	 * Set and get a specific page
	 *
	 * @param   mixed   $id  Integer or string of page to look up
	 * @return  object  WikiModelPage
	 */
	public function page($id=null)
	{
		if (!isset($this->page) && $id === null)
		{
			$pagename = trim(Request::getVar('pagename', '', 'default', 'none', 2));

			// Clean the path. Since path is built of a chain of pagenames
			// the wiki normalize() should strip any nasty stuff out
			$bits = explode('/', $pagename);
			$pagename = array_pop($bits);
			foreach ($bits as $i => $bit)
			{
				$bits[$i] = Page::normalize($bit);
			}
			$path = implode('/', $bits);

			if (substr(strtolower($pagename), 0, strlen('image:')) != 'image:'
			 && substr(strtolower($pagename), 0, strlen('file:')) != 'file:')
			{
				$pagename = Page::normalize($pagename);
			}
			Request::setVar('pagename', ($path ? $path . '/' : '') . $pagename);

			$task = trim(Request::getWord('task', ''));

			// No page name given! Default to the home page
			if (!$pagename && $task != 'new')
			{
				return $this->main();
			}

			// Load the page
			$this->page = Page::oneByPath(($path ? $path . '/' : '') . $pagename, $this->get('scope'), $this->get('scope_id'));

			if (!$this->page->get('id') && $this->page->getNamespace() == 'help')
			{
				$this->page = Page::oneByPath($pagename, 'site', 0);
				$this->page->set('scope', $this->get('scope'));
				$this->page->set('scope_id', $this->get('scope_id'));
			}

			if (!$this->page->get('id'))
			{
				$this->page->set('pagename', $pagename);
				$this->page->set('path', $path);
				$this->page->set('scope', $this->get('scope'));
				$this->page->set('scope_id', $this->get('scope_id'));
			}
		}

		if (!isset($this->page)
		 || (
				$id !== null
			 && (int) $this->page->get('id') != $id
			 && (string) $this->page->get('pagename') != Page::normalize($id)
			)
		 )
		{
			$this->page = Page::oneByPath($id, $this->get('scope'), $this->get('scope_id'));
		}

		return $this->page;
	}

	/**
	 * Get the main page
	 *
	 * @return  object  WikiModelPage
	 */
	public function main()
	{
		/*$this->page = null;

		if (isset($this->_cache['pages']) && ($this->_cache['pages'] instanceof ItemList))
		{
			foreach ($this->_cache['pages'] as $page)
			{
				if ($page->get('main', 0) == 1)
				{
					$this->page = $page;
					break;
				}
			}
		}

		if ($this->page)
		{
			return $this->page;
		}*/

		return $this->page($this->config('homepage', 'MainPage'));
	}

	/**
	 * Get a count or list of pages
	 *
	 * @param   array  $filters  Filters to apply
	 * @return  mixed
	 */
	public function pages($filters=array())
	{
		$pages = Page::all();

		if (!isset($filters['scope']))
		{
			$filters['scope'] = $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = $this->get('scope_id');
		}

		if ($filters['scope'])
		{
			$pages->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'])
		{
			$pages->whereEquals('scope_id', $filters['scope_id']);
		}

		if (isset($filters['namespace']))
		{
			$pages->whereEquals('namespace', $filters['namespace']);
		}

		if (isset($filters['parent']))
		{
			$pages->whereEquals('parent', $filters['parent']);
		}

		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}
			$pages->whereIn('access', $filters['access']);
		}

		if (isset($filters['state']))
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$pages->whereIn('state', $filters['state']);
		}

		return $pages;
	}

	/**
	 * Get page templates
	 *
	 * @param   array  $filters  Filters to apply
	 * @return  array
	 */
	public function templates($filters=array())
	{
		$filters['namespace'] = 'Template';

		return $this->pages($filters)->order('title', 'asc');
	}

	/**
	 * Get a list of special pages
	 *
	 * @return  array
	 */
	public function special()
	{
		static $pages;

		if (!isset($pages))
		{
			$path = dirname(__DIR__) . DS . 'site' . DS . 'views' . DS . 'special' . DS . 'tmpl';

			$pages = array();

			if (is_dir($path))
			{
				// Loop through all files and find views
				$dirIterator = new \DirectoryIterator($path);

				foreach ($dirIterator as $file)
				{
					if ($file->isDot() || $file->isDir())
					{
						continue;
					}

					if ($file->isFile())
					{
						$name = $file->getFilename();

						if (Filesystem::extension($name) != 'php'
						 || 'cvs' == strtolower($name)
						 || '.svn' == strtolower($name))
						{
							continue;
						}

						$pages[] = strtolower(Filesystem::name($name));
					}
				}

				sort($pages);
			}
		}

		return $pages;
	}
}
