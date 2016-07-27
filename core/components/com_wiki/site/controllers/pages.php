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

namespace Components\Wiki\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Version;
use Components\Wiki\Models\Author;
use Components\Wiki\Models\Attachment;
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
class Pages extends SiteController
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

		if (!isset($config['scope']))
		{
			$config['scope'] = 'site';
		}

		if (!isset($config['scope_id']))
		{
			$config['scope_id'] = 0;
		}

		$this->book = new Book($config['scope'], $config['scope_id']);

		if ($config['scope'] != 'site')
		{
			Request::setVar('task', Request::getWord('action'));
		}

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		/*if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			//App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiSetup') : null;
		}*/

		$this->page = $this->book->page();

		if (in_array($this->page->getNamespace(), array('image', 'file')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download')
			);
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
		// Set the page's <title> tag
		if ($this->page->get('scope') == 'site')
		{
			Document::setTitle(Lang::txt('COM_WIKI'));
		}

		Document::setTitle(Document::getTitle() . ': ' . $this->page->title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		// Is this a special page?
		if ($this->page->getNamespace() == 'special')
		{
			// Ensure the special page exists
			if (!in_array(strtolower($this->page->stripNamespace()), $this->book->special()))
			{
				App::abort(404, Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST'));
			}

			$this->view
				->setLayout('special')
				->set('layout', $this->page->stripNamespace())
				->set('page', $this->page)
				->set('book', $this->book)
				->set('sub', $this->page->get('scope') != 'site')
				->display();
			return;
		}

		// Does a page exist for the given pagename?
		if ($this->page->isNew() || $this->page->isDeleted())
		{
			if (!$this->page->access('create'))
			{
				App::abort(404, Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST'));
			}

			$this->view
				->set('page', $this->page)
				->set('book', $this->book)
				->set('sub', $this->page->get('scope') != 'site')
				->setLayout('doesnotexist')
				->display();
			return;
		}

		/*if ($this->page->get('scope') != $this->book->get('scope'))
		{
			App::redirect(
				Route::url($this->page->link())
			);
		}*/

		// Check if the page is group restricted and the user is authorized
		if (!$this->page->access('view', 'page'))
		{
			App::abort(403, Lang::txt('COM_WIKI_WARNING_NOT_AUTH'));
		}

		$parents = array();

		if ($this->page->get('parent'))
		{
			$parents = $this->page->ancestors();

			foreach ($parents as $p)
			{
				Pathway::append(
					$p->get('title'),
					$p->link()
				);
			}
		}

		Pathway::append(
			$this->page->title,
			$this->page->link()
		);

		// Retrieve a specific version if given
		if ($version = Request::getInt('version', 0))
		{
			$revision = $this->page->versions()
				->whereEquals('version', $version)
				->row();
		}
		else
		{
			$revision = $this->page->version;
		}

		if (!$revision->get('id'))
		{
			$this->view
				->set('page', $this->page)
				->set('version', ($version ? $version : $this->page->get('version_id')))
				->set('book', $this->book)
				->set('sub', $this->page->get('scope') != 'site')
				->setLayout('nosuchrevision')
				->display();
			return;
		}

		// Parse the text
		if (intval($this->book->config('cache', 1)))
		{
			// Caching
			if (!($rendered = Cache::get('wiki.r' . $revision->get('id'))))
			{
				$rendered = $revision->content($this->page);

				Cache::put('wiki.r' . $revision->get('id'), $rendered, intval($this->book->config('cache_time', 15)));
			}
			$revision->set('pagehtml', $rendered);
		}
		else
		{
			$revision->set('pagehtml', $revision->content($this->page));
		}

		//App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiParse') : null;

		// Handle display events
		$event = new \stdClass();

		$results = Event::trigger('wiki.onAfterDisplayTitle', array($this->page, &$revision, $this->config));
		$event->afterDisplayTitle = trim(implode("\n", $results));

		$results = Event::trigger('wiki.onBeforeDisplayContent', array(&$this->page, &$revision, $this->config));
		$event->beforeDisplayContent = trim(implode("\n", $results));

		$results = Event::trigger('wiki.onAfterDisplayContent', array(&$this->page, &$revision, $this->config));
		$event->afterDisplayContent = trim(implode("\n", $results));

		$this->page->set('event', $event);

		// Output view
		if (Request::getVar('format') == 'raw')
		{
			$this->view->setLayout('display_raw');
		}

		$this->view
			->set('page', $this->page)
			->set('revision', $revision)
			->set('parents', $parents)
			->set('sub', $this->page->get('scope') != 'site')
			->set('base_path', $this->_base_path)
			->setErrors($this->getErrors())
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
	 * @param   object  $revision
	 * @return  void
	 */
	public function editTask($revision = null)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getVar('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Check if the page is locked and the user is authorized
		if ($this->page->isLocked() && !$this->page->access('manage'))
		{
			App::redirect(
				Route::url($this->page->link()),
				Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
		}

		// Check if the page is restricted and the user is authorized
		if (!$this->page->access('edit') && !$this->page->access('modify'))
		{
			App::redirect(
				Route::url($this->page->link()),
				Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
		}

		// Load the page
		$ischild = false;
		if ($this->page->get('id') && $this->_task == 'new')
		{
			$this->page->set('id', 0);
			$ischild = true;
		}

		// Get the most recent version for editing
		if (!is_object($revision))
		{
			$revision = $this->page->version;
			$revision->set('created_by', User::get('id'));
			$revision->set('summary', '');
		}

		// If an existing page, pull its tags for editing
		if (!$this->page->exists())
		{
			$this->page->set('access', 0);
			$this->page->set('created_by', User::get('id'));
			$this->page->set('scope', $this->book->get('scope'));
			$this->page->set('scope_id', $this->book->get('scope_id'));

			if ($ischild && $this->page->get('pagename'))
			{
				$this->revision->set('pagetext', '');

				$this->page->set('path', $this->page->get('path') . ($this->page->get('path') ? '/' : '') . $this->page->get('pagename'));
				$this->page->set('pagename', '');
				$this->page->set('title', Lang::txt('COM_WIKI_NEW_PAGE'));
			}
		}

		$tags = trim(Request::getVar('tags', $this->page->tags('string'), 'post'));
		//$authors = trim(Request::getVar('authors', $this->page->authors('string'), 'post'));

		// Set the page's <title> tag
		if ($this->page->get('scope') == 'site')
		{
			Document::setTitle(Lang::txt('COM_WIKI'));
		}

		Document::setTitle(
			Document::getTitle() . ': ' .
			$this->page->title . ': ' .
			Lang::txt(strtoupper($this->_option . '_' . $this->_task))
		);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}
		Pathway::append(
			$this->page->title,
			$this->page->link()
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			$this->page->link() . '&task=' . $this->_task
		);

		// Are we previewing?
		if ($this->preview)
		{
			$pageid = $this->page->get('id');
			$lid = Request::getInt('lid', 0, 'post');
			if ($lid != $this->page->get('id'))
			{
				$pageid = $lid;
			}

			// Parse the HTML
			$lid = Request::getInt('lid', 0, 'post');
			$pagename = $this->page->get('pagename');

			if ($lid != $this->page->get('id'))
			{
				$this->page->set('id', $lid);
			}

			$this->page->set('pagename', ($this->page->exists() ? $this->page->get('pagename') : 'Tmp:' . $pageid));

			$revision->set('pagehtml', $revision->content($this->page));

			$this->page->set('id', $pageid);
			$this->page->set('pagename', $pagename);
		}

		// Pull a tree of pages in this wiki
		$items = $this->book->pages()
			->whereEquals('state', Page::STATE_PUBLISHED)
			->where('namespace', '!=', 'Template')
			->order('pagename', 'asc')
			->rows();

		$tree = array();
		if ($items)
		{
			foreach ($items as $k => $branch)
			{
				// Since these will be parent pages, we need to add the item's pagename to the scope
				$branch->set('pagename', ($branch->get('path') ? $branch->get('path') . '/' : '') . $branch->get('pagename'));

				// Push the item to the tree
				$tree[$branch->get('pagename')] = $branch;
			}
			ksort($tree);
		}

		$this->view
			->set('book', $this->book)
			->set('page', $this->page)
			->set('revision', $revision)
			->set('sub', $this->page->get('scope') != 'site')
			->set('tree', $tree)
			->set('tags', $tags)
			->set('preview', $this->preview)
			->set('base_path', $this->_base_path)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
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
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Incoming revision
		$revision = $this->page->version;
		$revision->set('version', $revision->get('version') + 1);
		$revision->set(Request::getVar('revision', array(), 'post', 'none', 2));
		$revision->set('id', 0);

		// Incoming page
		$page = Request::getVar('page', array(), 'post', 'none', 2);
		if (!isset($page['protected']) || !$page['protected'])
		{
			$page['protected'] = 0;
		}

		$this->page = Page::oneOrNew(intval($revision->get('page_id')));
		$this->page->set($page);
		$this->page->set('pagename', trim(Request::getVar('pagename', '', 'post')));

		// Get parameters
		$params = new \Hubzero\Config\Registry($this->page->get('params', ''));
		$params->merge(Request::getVar('params', array(), 'post'));

		$this->page->set('params', $params->toString());

		// Get the previous version to compare against
		if (!$revision->get('page_id'))
		{
			// New page - save it to the database
			$this->page->set('created_by', User::get('id'));

			$old = Version::blank();
		}
		else
		{
			// Get the revision before changes
			$old = $this->page->version;
		}

		// Was the preview button pushed?
		$this->preview = trim(Request::getVar('preview', ''));

		if ($this->preview)
		{
			// Set the component task
			if (!$page['id'])
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
			return $this->editTask($revision);
		}

		// Check content
		// First, make sure the pagetext isn't empty
		if ($revision->get('pagetext') == '')
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_MISSING_PAGETEXT'));
			return $this->editTask($revision);
		}

		// Store new content
		if (!$this->page->save())
		{
			$this->setError($this->page->getError());
			return $this->editTask($revision);
		}

		// Get allowed authors
		if (!Author::setForPage(Request::getVar('authors', '', 'post'), $this->page->get('id')))
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_SAVING_AUTHORS'));
			return $this->editTask($revision);
		}

		// Get the upload path
		$path = Attachment::blank()->filespace();

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

				foreach (Attachment::all()->whereEquals('page_id', $lid) as $attachment)
				{
					$attachment->set('page_id', $this->page->get('id'));
					$attachment->save();
				}
			}
		}

		$revision->set('page_id', $this->page->get('id'));
		$revision->set('version', $revision->get('version') + 1);

		if ($this->page->param('mode', 'wiki') == 'knol')
		{
			// Set revisions to NOT approved
			$revision->set('approved', 0);
			// If an author or the original page creator, set to approved
			if ($this->page->get('created_by') == User::get('id')
			 || $this->page->isAuthor(User::get('id')))
			{
				$revision->set('approved', 1);
			}
		}
		else
		{
			// Wiki mode, approve revision
			$revision->set('approved', 1);
		}

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if (rtrim($old->get('pagetext')) != rtrim($revision->get('pagetext')))
		{
			// Transform the wikitext to HTML
			$revision->set('pagehtml', '');
			$revision->set('pagehtml', $revision->content($this->page));

			if ($this->page->access('manage') || $this->page->access('edit'))
			{
				$revision->set('approved', 1);
			}

			// Store content
			if (!$revision->save())
			{
				$this->setError(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'));
				return $this->editTask($revision);
			}

			$this->page->set('version_id', $revision->get('id'));
			$this->page->set('modified', $revision->get('created'));
		}
		else
		{
			$this->page->set('modified', Date::toSql());
		}

		if (!$this->page->save())
		{
			// This really shouldn't happen.
			$this->setError(Lang::txt('COM_WIKI_ERROR_SAVING_PAGE'));
			return $this->editTask($revision);
		}

		// Process tags
		$this->page->tag(Request::getVar('tags', ''));

		// Log activity
		$recipients = array(
			['wiki.site', 1],
			['user', $this->page->get('created_by')],
			['user', $revision->get('created_by')]
		);
		if ($this->page->get('scope') != 'site')
		{
			$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
			$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($page['id'] ? 'updated' : 'created'),
				'scope'       => 'wiki.page',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_PAGE_' . ($page['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
				'details'     => array(
					'title'    => $this->page->title,
					'url'      => Route::url($this->page->link()),
					'name'     => $this->page->get('pagename'),
					'revision' => $revision->get('id')
				)
			],
			'recipients' => $recipients
		]);

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
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		if (!is_object($this->page))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'),
				'error'
			);
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('delete'))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
		}

		$confirmed = Request::getInt('confirm', 0, 'post');

		switch ($confirmed)
		{
			case 1:
				// Check for request forgeries
				Request::checkToken();

				$this->page->set('state', \Components\Wiki\Models\Page::STATE_DELETED);
				if (!$this->page->save())
				{
					$this->setError(Lang::txt('COM_WIKI_UNABLE_TO_DELETE'));
				}

				$this->page->log('page_removed');

				Cache::clean('wiki');

				// Log activity
				$recipients = array(
					['wiki.site', 1],
					['user', $this->page->get('created_by')]
				);
				if ($this->page->get('scope') != 'site')
				{
					$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
					$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'deleted',
						'scope'       => 'wiki.page',
						'scope_id'    => $this->page->get('id'),
						'description' => Lang::txt('COM_WIKI_ACTIVITY_PAGE_DELETED', '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
						'details'     => array(
							'title' => $this->page->title,
							'url'   => Route::url($this->page->link()),
							'name'  => $this->page->get('pagename')
						)
					],
					'recipients' => $recipients
				]);
			break;

			default:
				// Set the page's <title> tag
				Document::setTitle(
					Lang::txt(strtoupper($this->_option)) . ': ' .
					$this->page->title . ': ' .
					Lang::txt(strtoupper($this->_option . '_' . $this->_task))
				);

				// Set the pathway
				if (Pathway::count() <= 0)
				{
					Pathway::append(
						Lang::txt(strtoupper($this->_option)),
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
				}

				$parents = array();

				if ($this->page->get('parent'))
				{
					$parents = $this->page->ancestors();

					foreach ($parents as $p)
					{
						Pathway::append(
							$p->get('title'),
							$p->link()
						);
					}
				}

				Pathway::append(
					$this->page->title,
					$this->page->link()
				);
				Pathway::append(
					Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
					$this->page->link('delete')
				);

				$this->view
					->set('book', $this->book)
					->set('page', $this->page)
					->set('base_path', $this->_base_path)
					->set('parents', $parents)
					->set('sub', $this->page->get('scope') != 'site')
					->setErrors($this->getErrors())
					->display();
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
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('edit'))
		{
			App::redirect(
				Route::url($this->page->link('base')),
				Lang::txt('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
		}

		// Set the page's <title> tag
		Document::setTitle(
			Lang::txt(strtoupper($this->_name)) . ': ' .
			$this->page->title . ': ' .
			Lang::txt('RENAME')
		);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		$parents = array();

		if ($this->page->get('parent'))
		{
			$parents = $this->page->ancestors();

			foreach ($parents as $p)
			{
				Pathway::append(
					$p->get('title'),
					$p->link()
				);
			}
		}

		Pathway::append(
			$this->page->title,
			$this->page->link()
		);
		Pathway::append(
			Lang::txt(strtoupper('COM_WIKI_RENAME')),
			$this->page->link('rename')
		);

		// Output HTML
		$this->view
			->set('book', $this->book)
			->set('page', $this->page)
			->set('parents', $parents)
			->set('base_path', $this->_base_path)
			->set('sub', $this->page->get('scope') != 'site')
			->setErrors($this->getErrors())
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
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Incoming
		$oldpagename = trim(Request::getVar('oldpagename', '', 'post'));
		$newpagename = trim(Request::getVar('newpagename', '', 'post'));

		// Load the page
		$this->page = Page::oneByPath($oldpagename, $this->book->get('scope'), $this->book->get('scope_id'));

		$newpagename = $this->page->normalize($newpagename);

		// Are they just changing case of characters?
		if (strtolower($this->page->get('pagename')) == strtolower($newpagename))
		{
			$this->setError(Lang::txt('New name matches old name.'));
			return $this->renameTask();
		}

		// Check that no other pages are using the new title
		$p = Page::oneByPath($newpagename, $this->page->get('scope'), $this->page->get('scope_id'));
		if ($p->exists())
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_PAGE_EXIST') . ' ' . Lang::txt('CHOOSE_ANOTHER_PAGENAME'));
			return $this->renameTask();
		}

		$this->page->set('pagename', $newpagename);

		if (!$this->page->save())
		{
			$this->setError($this->page->getError());
			return $this->renameTask();
		}

		$pages = Page::all()
			->whereEquals('parent', $this->page->get('id'))
			->rows();

		foreach ($pages as $page)
		{
			$page->save();
		}

		$this->page->log('page_renamed');

		// Log activity
		$recipients = array(
			['wiki.site', 1],
			['user', $this->page->get('created_by')]
		);
		if ($this->page->get('scope') != 'site')
		{
			$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
			$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => 'wiki.page',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_PAGE_RENAMED', '<a href="' . Route::url($this->page->link()) . '">' . $this->page->get('title') . '</a>'),
				'details'     => array(
					'title' => $this->page->get('title'),
					'url'   => Route::url($this->page->link()),
					'name'  => $this->page->get('pagename')
				)
			],
			'recipients' => $recipients
		]);

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
	 * @return  void
	 */
	public function pdfTask()
	{
		// Does a page exist for the given pagename?
		if (!$this->page->exists() || $this->page->isDeleted())
		{
			App::abort(404, Lang::txt('COM_WIKI_WARNING_NOT_FOUND'));
		}

		// Retrieve a specific version if given
		if ($version = Request::getInt('version', 0))
		{
			$revision = $this->page->versions()
				->whereEquals('version', $version)
				->whereEquals('approved', 1)
				->row();
		}
		else
		{
			$revision = $this->page->version;
		}

		if (!$revision->exists())
		{
			$this->view
				->set('page', $this->page)
				->set('version', $version)
				->set('sub', $this->page->get('scope') != 'site')
				->setLayout('nosuchrevision')
				->display();
			return;
		}

		// Log activity
		$recipients = array(
			['wiki.site', 1]
		);
		if ($this->page->get('scope') != 'site')
		{
			$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
			$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'downloaded',
				'scope'       => 'wiki.page',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_PAGE_DOWNLOADED', '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
				'details'     => array(
					'title' => $this->page->title,
					'url'   => Route::url($this->page->link()),
					'name'  => $this->page->get('pagename')
				)
			],
			'recipients' => $recipients
		]);

		Request::setVar('format', 'pdf');

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set font
		//$pdf->SetFont('dejavusans', '', 11, '', true);

		$pdf->setAuthor  = $this->page->creator()->get('name');
		$pdf->setCreator = \Config::get('sitename');

		$pdf->setDocModificationTimeStamp($this->page->modified());
		$pdf->setHeaderData(NULL, 0, strtoupper($this->page->title), NULL, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		$pdf->AddPage();

		// Parse wiki content
		$revision->set('pagehtml', $revision->content($this->page));

		// Set the view page content to current revision html
		$this->view
			->set('page', $this->page)
			->set('revision', $revision);

		$pdf->writeHTML($this->view->loadTemplate(), true, false, true, false, '');

		header("Content-type: application/octet-stream");

		// Close and output PDF document
		// Force the download of the PDF
		$pdf->Output($this->page->get('pagename') . '.pdf', 'D');
		exit();
	}
}
