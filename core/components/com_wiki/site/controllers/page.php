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

namespace Components\Wiki\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page as Article;
use Components\Wiki\Models\Revision;
use Components\Wiki\Helpers\Parser;
use Components\Wiki\Tables;
use Exception;
use Pathway;
use Request;
use Event;
use User;
use Lang;
use Date;

/**
 * Wiki controller class for pages
 */
class Page extends SiteController
{
	/**
	 * Book model
	 *
	 * @var  object
	 */
	public $book = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations
	 * @return  void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = dirname(__DIR__);
		if (isset($config['base_path']))
		{
			$this->_base_path = $config['base_path'];
		}

		$this->_sub = false;
		if (isset($config['sub']))
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group']))
		{
			$this->_group = $config['group'];
		}

		if ($this->_sub)
		{
			Request::setVar('task', Request::getWord('action'));
		}

		$this->book = new Book(($this->_group ? $this->_group : '__site__'));

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiSetup') : null;
		}

		$this->page = $this->book->page();

		if (in_array($this->page->get('namespace'), array('image', 'file')))
		{
			App::redirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download'
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Display a page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->book      = $this->book;
		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Prep the pagename for display
		$this->view->title = $this->page->get('title'); //getTitle();

		// Set the page's <title> tag
		if ($this->_sub)
		{
			Document::setTitle(Document::getTitle() . ': ' . $this->view->title);
		}
		else
		{
			Document::setTitle(($this->_sub ? Lang::txt('COM_GROUPS') . ': ' : '') . Lang::txt('COM_WIKI') . ': ' . $this->view->title);
		}

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		// Is this a special page?
		if ($this->page->get('namespace') == 'special')
		{
			// Set the layout
			$this->view->setLayout('special');
			$this->view->layout = $this->page->denamespaced();
			$this->view->page->set('scope', Request::getVar('scope', ''));
			$this->view->page->set('group_cn', $this->_group);
			$this->view->message = $this->_message;

			// Ensure the special page exists
			if (!in_array(strtolower($this->view->layout), $this->book->special()))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&scope=' . $this->view->page->get('scope'))
				);
				return;
			}

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->display();
			return;
		}

		// Does a page exist for the given pagename?
		if (!$this->page->exists() || $this->page->isDeleted())
		{
			if (!$this->page->access('create'))
			{
				App::abort(404, Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST'));
			}

			// No! Ask if they want to create a new page
			$this->view->setLayout('doesnotexist');
			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/wiki');
			}

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->display();
			return;
		}

		if ($this->page->get('group_cn') && !$this->_group)
		{
			App::redirect(
				Route::url('index.php?option=com_groups&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename'))
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->page->access('view', 'page'))
		{
			throw new Exception(Lang::txt('COM_WIKI_WARNING_NOT_AUTH'), 403);
		}

		$parents = array();
		if ($scope = $this->page->get('scope'))
		{
			$s = array();
			if ($cn = $this->page->get('group_cn'))
			{
				$scope = substr($scope, strlen($cn . '/wiki'));
				$s[] = $cn;
				$s[] = 'wiki';
			}
			$scope = trim($scope, '/');
			if ($scope)
			{
				$bits = explode('/', $scope);
				foreach ($bits as $bit)
				{
					$bit = trim($bit);
					if ($bit != '/' && $bit != '')
					{
						$p = Article::getInstance($bit, implode('/', $s));
						if ($p->exists())
						{
							Pathway::append(
								$p->get('title'),
								$p->link()
							);
							$parents[] = $p;
						}
						$s[] = $bit;
					}
				}
			}
		}

		Pathway::append(
			$this->view->title,
			$this->page->link()
		);

		// Retrieve a specific version if given
		$this->view->version  = Request::getInt('version', 0);
		$this->view->revision = $this->page->revision($this->view->version);

		if (!$this->view->revision->exists())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view
				->setLayout('nosuchrevision')
				->display();
			return;
		}

		if (Request::getVar('format', '') == 'raw')
		{
			Request::setVar('no_html', 1);

			echo nl2br($this->view->revision->get('pagetext'));
			return;
		}
		elseif (Request::getVar('format', '') == 'printable')
		{
			echo $this->view->revision->get('pagehtml');
			return;
		}

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->page->get('scope'),
			'pagename' => $this->page->get('pagename'),
			'pageid'   => $this->page->get('id'),
			'filepath' => '',
			'domain'   => $this->page->get('group_cn')
		);

		$p = Parser::getInstance();

		// Parse the text
		if (intval($this->book->config('cache', 1)))
		{
			// Caching
			if (!($rendered = Cache::get('wiki.r' . $this->view->revision->get('id'))))
			{
				$rendered = $p->parse($this->view->revision->get('pagetext'), $wikiconfig, true, true);

				Cache::put('wiki.r' . $this->view->revision->get('id'), $rendered, intval($this->book->config('cache_time', 15)));
			}
			$this->view->revision->set('pagehtml', $rendered);
		}
		else
		{
			$this->view->revision->set('pagehtml', $p->parse($this->view->revision->get('pagetext'), $wikiconfig, true, true));
		}

		App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiParse') : null;

		// Handle display events
		$this->page->event = new \stdClass();

		$results = Event::trigger('wiki.onAfterDisplayTitle', array($this->page, &$this->view->revision, $this->config));
		$this->page->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = Event::trigger('wiki.onBeforeDisplayContent', array(&$this->page, &$this->view->revision, $this->config));
		$this->page->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = Event::trigger('wiki.onAfterDisplayContent', array(&$this->page, &$this->view->revision, $this->config));
		$this->page->event->afterDisplayContent = trim(implode("\n", $results));

		$this->view->message = $this->_message;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('parents', $parents)
			->display();
	}

	/**
	 * Show a form for creating an entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Check if the page is locked and the user is authorized
		if ($this->page->get('state') == 1 && !$this->page->access('manage'))
		{
			App::redirect(
				Route::url($this->page->link()),
				Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->page->access('edit') && !$this->page->access('modify'))
		{
			App::redirect(
				Route::url($this->page->link()),
				Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		$this->view->setLayout('edit');

		// Load the page
		$ischild = false;
		if ($this->page->get('id') && $this->_task == 'new')
		{
			$this->page->set('id', 0);
			$ischild = true;
		}

		// Get the most recent version for editing
		if (!is_object($this->revision))
		{
			$this->revision = $this->page->revision('current'); //getCurrentRevision();
			$this->revision->set('created_by', User::get('id'));
			$this->revision->set('summary', '');
		}

		// If an existing page, pull its tags for editing
		if (!$this->page->exists())
		{
			$this->page->set('access', 0);
			$this->page->set('created_by', User::get('id'));

			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/' . $this->_sub);
			}

			if ($ischild && $this->page->get('pagename'))
			{
				$this->revision->set('pagetext', '');
				$this->page->set('scope', $this->page->get('scope') . ($this->page->get('scope') ? '/' . $this->page->get('pagename') : $this->page->get('pagename')));
				$this->page->set('pagename', '');
				$this->page->set('title', Lang::txt('COM_WIKI_NEW_PAGE'));
			}
		}

		$this->view->tags = trim(Request::getVar('tags', $this->page->tags('string'), 'post'));
		$this->view->authors = trim(Request::getVar('authors', $this->page->authors('string'), 'post'));

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = (trim($this->page->get('title')) ? $this->page->get('title') : Lang::txt('COM_WIKI_NEW_PAGE'));

		// Set the page's <title> tag
		if ($this->_sub)
		{
			Document::setTitle(Document::getTitle() . ': ' . $this->view->title);
		}
		else
		{
			Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task)));
		}

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}
		if (!$this->_sub)
		{
			Pathway::append(
				$this->view->title,
				$this->page->link()
			);
			Pathway::append(
				Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
				$this->page->link() . '&task=' . $this->_task
			);
		}

		$this->view->preview = NULL;

		// Are we previewing?
		if ($this->preview)
		{
			// Yes - get the preview so we can parse it and display
			$this->view->preview = $this->preview;

			$pageid = $this->page->get('id');
			$lid = Request::getInt('lid', 0, 'post');
			if ($lid != $this->page->get('id'))
			{
				$pageid = $lid;
			}

			// Parse the HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => $this->page->get('scope'),
				'pagename' => ($this->page->exists() ? $this->page->get('pagename') : 'Tmp:' . $pageid),
				'pageid'   => $pageid,
				'filepath' => '',
				'domain'   => $this->_group
			);

			$p = Parser::getInstance();

			$this->revision->set('pagehtml', $p->parse($this->revision->get('pagetext'), $wikiconfig, true, true));
		}

		$this->view->sub       = $this->_sub;
		$this->view->base_path = $this->_base_path;
		$this->view->message   = $this->_message;
		$this->view->page      = $this->page;
		$this->view->book      = $this->book;
		$this->view->revision  = $this->revision;

		// Pull a tree of pages in this wiki
		$items = $this->book->pages('list', array(
			'group'  => $this->_group,
			'sortby' => 'pagename ASC, scope ASC',
			'state'  => array(0, 1)
		));
		$tree = array();
		if ($items)
		{
			foreach ($items as $k => $branch)
			{
				// Since these will be parent pages, we need to add the item's pagename to the scope
				$branch->set('scope', ($branch->get('scope') ? $branch->get('scope') . '/' . $branch->get('pagename') : $branch->get('pagename')));
				$branch->set('scopeName', $branch->get('scope'));
				// Strip the group name from the beginning of the scope for display.
				if ($this->_group)
				{
					$branch->set('scopeName', substr($branch->get('scope'), strlen($this->_group . '/wiki/')));
				}
				// Push the item to the tree
				$tree[$branch->get('scope')] = $branch;
			}
			ksort($tree);
		}
		$this->view->tree = $tree; //$items;

		$this->view->tplate = trim(Request::getVar('tplate', ''));

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Save a wiki page
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Incoming revision
		$rev = Request::getVar('revision', array(), 'post', 'none', 2);
		//$rev['pageid'] = (isset($rev['pageid'])) ? intval($rev['pageid']) : 0;

		$this->revision = $this->page->revision('current');
		$this->revision->set('version', $this->revision->get('version') + 1);
		if (!$this->revision->bind($rev))
		{
			$this->setError($this->revision->getError());
			$this->editTask();
			return;
		}
		$this->revision->set('id', 0);

		// Incoming page
		$page = Request::getVar('page', array(), 'post', 'none', 2);

		$this->page = new Article(intval($rev['pageid']));
		if (!$this->page->bind($page))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}
		$this->page->set('pagename', trim(Request::getVar('pagename', '', 'post')));
		$this->page->set('scope', trim(Request::getVar('scope', '', 'post')));
		if (!isset($page['state']))
		{
			$this->page->set('state', 0);
		}

		// Get parameters
		$params = new \Hubzero\Config\Registry($this->page->get('params', ''));
		$params->merge(Request::getVar('params', array(), 'post'));

		$this->page->set('params', $params->toString());

		// Get the previous version to compare against
		if (!$rev['pageid'])
		{
			// New page - save it to the database
			$this->page->set('created_by', User::get('id'));

			$old = new Revision(0);
		}
		else
		{
			// Get the revision before changes
			$old = $this->page->revision('current');
		}

		// Was the preview button pushed?
		$this->preview = trim(Request::getVar('preview', ''));
		if ($this->preview)
		{
			// Set the component task
			if (!$rev['pageid'])
			{
				Request::setVar('task', 'new');
				$this->_task = 'new';
			}
			else
			{
				Request::setVar('task', 'edit');
				$this->_task = 'edit';
			}

			// Push on through to the edit form
			$this->editTask();
			return;
		}

		// Check content
		// First, make sure the pagetext isn't empty
		if ($this->revision->get('pagetext') == '')
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_MISSING_PAGETEXT'));
			$this->editTask();
			return;
		}

		// Store new content
		if (!$this->page->store(true))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}

		// Get allowed authors
		if (!$this->page->updateAuthors(Request::getVar('authors', '', 'post')))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}

		// Get the upload path
		$wpa = new Tables\Attachment($this->database);
		$path = $wpa->filespace();

		// Rename the temporary upload directory if it exist
		$lid = Request::getInt('lid', 0, 'post');
		if ($lid != $this->page->get('id'))
		{
			if (is_dir($path . DS . $lid))
			{
				if (!\Filesystem::move($path . DS . $lid, $path . DS . $this->page->get('id')))
				{
					$this->setError(\Filesystem::move($path . DS . $lid, $path . DS . $this->page->get('id')));
				}
				$wpa->setPageID($lid, $this->page->get('id'));
			}
		}

		$this->revision->set('pageid',   $this->page->get('id'));
		$this->revision->set('pagename', $this->page->get('pagename'));
		$this->revision->set('scope',    $this->page->get('scope'));
		$this->revision->set('group_cn', $this->page->get('group_cn'));
		$this->revision->set('version',  $this->revision->get('version') + 1);

		if ($this->page->param('mode', 'wiki') == 'knol')
		{
			// Set revisions to NOT approved
			$this->revision->set('approved', 0);
			// If an author or the original page creator, set to approved
			if ($this->page->get('created_by') == User::get('id')
			 || $this->page->isAuthor(User::get('id')))
			{
				$this->revision->set('approved', 1);
			}
		}
		else
		{
			// Wiki mode, approve revision
			$this->revision->set('approved', 1);
		}

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if (rtrim($old->get('pagetext')) != rtrim($this->revision->get('pagetext')))
		{
			// Transform the wikitext to HTML
			$this->revision->set('pagehtml', '');
			$this->revision->set('pagehtml', $this->revision->content('parsed'));

			// Parse attachments
			/*$a = new Tables\Attachment($this->database);
			$a->pageid = $this->page->id;
			$a->path = $path;

			$this->revision->pagehtml = $a->parse($this->revision->pagehtml);*/
			if ($this->page->access('manage') || $this->page->access('edit'))
			{
				$this->revision->set('approved', 1);
			}

			// Store content
			if (!$this->revision->store(true))
			{
				$this->setError(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'));
				$this->editTask();
				return;
			}

			if ($this->revision->get('approved'))
			{
				$this->page->set('version_id', $this->revision->get('id'));
			}
			$this->page->set('modified', $this->revision->get('created'));
		}
		else
		{
			$this->page->set('modified', Date::toSql());
		}

		if (!$this->page->store(true))
		{
			// This really shouldn't happen.
			$this->setError(Lang::txt('COM_WIKI_ERROR_SAVING_PAGE'));
			$this->editTask();
			return;
		}

		// Process tags
		$this->page->tag(Request::getVar('tags', ''));

		// Redirect
		App::redirect(
			Route::url($this->page->link())
		);
	}

	/**
	 * Delete a page
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		if (!is_object($this->page))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('delete'))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}

		$confirmed = Request::getInt('confirm', 0, 'post');

		switch ($confirmed)
		{
			case 1:
				// Check for request forgeries
				Request::checkToken();

				$this->page->set('state', 2);
				if (!$this->page->store(false, 'page_removed'))
				{
					$this->setError(Lang::txt('COM_WIKI_UNABLE_TO_DELETE'));
				}

				Cache::clean('wiki');
			break;

			default:
				$this->view->page      = $this->page;
				$this->view->config    = $this->config;
				$this->view->base_path = $this->_base_path;
				$this->view->sub       = $this->_sub;

				// Prep the pagename for display
				// e.g. "MainPage" becomes "Main Page"
				$this->view->title = $this->page->get('title');

				// Set the page's <title> tag
				Document::setTitle(Lang::txt(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task)));

				// Set the pathway
				if (Pathway::count() <= 0)
				{
					Pathway::append(
						Lang::txt(strtoupper($this->_option)),
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
				}
				Pathway::append(
					$this->view->title,
					$this->page->link()
				);
				Pathway::append(
					Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
					$this->page->link('delete')
				);

				$this->view->message = $this->_message;

				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}

				$this->view->display();
				return;
			break;
		}

		App::redirect(
			Route::url($this->page->link('base'))
		);
	}

	/**
	 * Show a form to rename a page
	 *
	 * @return  void
	 */
	public function renameTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('edit'))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}

		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . $this->view->title . ': ' . Lang::txt('RENAME'));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			$this->view->title,
			$this->page->link()
		);
		Pathway::append(
			Lang::txt(strtoupper('COM_WIKI_RENAME')),
			$this->page->link('rename')
		);

		$this->view->message = $this->_message;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view
			->setLayout('rename')
			->display();
	}

	/**
	 * Save the new page name
	 *
	 * @return  void
	 */
	public function saverenameTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Incoming
		$oldpagename = trim(Request::getVar('oldpagename', '', 'post'));
		$newpagename = trim(Request::getVar('newpagename', '', 'post'));
		$scope       = trim(Request::getVar('scope', '', 'post'));

		// Load the page
		$this->page = new Article($oldpagename, $scope);

		// Attempt to rename
		if (!$this->page->rename($newpagename))
		{
			$this->setError($this->page->getError());
			$this->renameTask();
			return;
		}

		// Redirect to the newly named page
		App::redirect(
			Route::url($this->page->link())
		);
	}

	/**
	 * Output the contents of a wiki page as a PDF
	 *
	 * Based on work submitted by Steven Maus <steveng4235@gmail.com> (2014)
	 *
	 * @return     void
	 */
	public function pdfTask()
	{
		// Does a page exist for the given pagename?
		if (!$this->page->exists() || $this->page->isDeleted())
		{
			App::abort(404, Lang::txt('COM_WIKI_WARNING_NOT_FOUND'));

			// No! Ask if they want to create a new page
			/*$this->view->setLayout('doesnotexist');
			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/wiki');
			}

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;*/
		}

		// Retrieve a specific version if given
		$this->view->revision = $this->page->revision(Request::getInt('version', 0));
		if (!$this->view->revision->exists())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view
				->set('page', $this->page)
				->setLayout('nosuchrevision')
				->display();
			return;
		}

		Request::setVar('format', 'pdf');

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set font
		$pdf->SetFont('dejavusans', '', 11, '', true);

		$pdf->setAuthor  = $this->page->creator('name');
		$pdf->setCreator = \Config::get('sitename');

		$pdf->setDocModificationTimeStamp($this->page->modified());
		$pdf->setHeaderData(NULL, 0, strtoupper($this->page->get('itle')), NULL, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		$pdf->AddPage();

		// Set the view page content to current revision html
		$this->view->page = $this->page;

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->page->get('scope'),
			'pagename' => $this->page->get('pagename'),
			'pageid'   => $this->page->get('id'),
			'filepath' => '',
			'domain'   => $this->page->get('group_cn')
		);

		$p = Parser::getInstance();

		// Parse the text
		$this->view->revision->set('pagehtml', $p->parse($this->view->revision->get('pagetext'), $wikiconfig, true, true));

		$pdf->writeHTML($this->view->loadTemplate(), true, false, true, false, '');

		header("Content-type: application/octet-stream");

		// Close and output PDF document
		// Force the download of the PDF
		$pdf->Output($this->page->get('pagename') . '.pdf', 'D');
		exit();
	}
}
