<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Base\Obj;
use Hubzero\Config\Registry;
use Components\Wiki\Helpers\Parser;
use Filesystem;
use Exception;
use Component;
use Request;
use Lang;

require_once __DIR__ . DS . 'page.php';

/**
 * Wiki model for a book
 */
class Book extends Obj
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
		$pages = $this->defaultPages();

		if (count($pages) <= 0)
		{
			return Lang::txt('No default pages found');
		}

		foreach ($pages as $f => $c)
		{
			$f = str_replace('_', ':', $f);

			// Instantiate a new page
			$page = Page::blank();
			$page->set('pagename', $f);
			$page->set('namespace', $page->getNamespace());
			$page->set('title', $page->title);
			$page->set('access', 0);
			$page->set('state', Page::STATE_PUBLISHED);
			$page->set('scope', $this->get('scope'));
			$page->set('scope_id', $this->get('scope_id'));

			$params = new Registry();

			if ($this->get('scope') == 'site' && $page->get('pagename') == 'MainPage')
			{
				$params->set('mode', 'static');
			}
			else
			{
				$params->set('mode', 'wiki');
			}

			$page->set('params', $params->toString());

			// Store content
			if (!$page->save())
			{
				throw new Exception($page->getError(), 500);
			}

			// Instantiate a new revision
			$revision = Version::blank();
			$revision->set('page_id', $page->get('id'));
			$revision->set('minor_edit', 0);
			$revision->set('version', 1);
			$revision->set('pagetext', $c);
			$revision->set('approved', 1);

			// Transform the wikitext to HTML
			if ($page->get('pagename') != 'Help:WikiMath')
			{
				$revision->set('pagehtml', $revision->content($page, $option));
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
	private function defaultPages()
	{
		$path = Parser::getInstance()->defaultPagesPath();

		if (!$path)
		{
			$path = dirname(__DIR__) . DS . 'default';
		}

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
			$pagename = trim(Request::getString('pagename', ''));

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
			$this->page = Page::oneByPath(
				($path ? $path . '/' : '') . $pagename,
				$this->get('scope'),
				$this->get('scope_id')
			);

			if (!$this->page->get('id') && $this->page->getNamespace($pagename) == 'help')
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
	 * @return  object
	 */
	public function main()
	{
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
