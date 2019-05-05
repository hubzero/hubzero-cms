<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Version;
use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Request;
use User;
use Lang;
use App;

/**
 * Wiki controller class for page history
 */
class History extends SiteController
{
	/**
	 * Book model
	 *
	 * @var  object
	 */
	public $book = null;

	/**
	 * Sub component?
	 *
	 * @var  bool
	 */
	public $sub = false;

	/**
	 * Constructor
	 *
	 * @param      array $config Optional configurations
	 * @return     void
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

		if (isset($config['sub']))
		{
			$this->sub = $config['sub'];
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

			App::get('config')->get('debug') || App::get('config')->get('profile') ? App::get('profiler')->mark('afterWikiSetup') : null;
		}*/

		$this->page = $this->book->page();

		if (in_array($this->page->getNamespace(), array('image', 'file')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download')
			);
		}

		if (!$this->page->exists())
		{
			App::abort(404, Lang::txt('COM_WIKI_WARNING_NOT_FOUND'));
		}

		if (is_null($this->sub))
		{
			$this->sub = ($this->page->get('scope') != 'site');
		}

		$this->registerTask('deleterevision', 'delete');

		parent::execute();
	}

	/**
	 * Display a history of the current wiki page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
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
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			$this->page->link() . '&' . ($this->sub ? 'action' : 'task') . '=' . $this->_task
		);

		$this->view
			->set('parents', $parents)
			->set('page', $this->page)
			->set('sub', $this->sub)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}

	/**
	 * Compare two versions of a wiki page
	 *
	 * @return  void
	 */
	public function compareTask()
	{
		include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Diff.php';

		// Incoming
		$oldid = Request::getInt('oldid', 0);
		$diff  = Request::getInt('diff', 0);

		// Do some error checking
		if (!$diff)
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_MISSING_VERSION'));
			return $this->displayTask();
		}

		if ($diff == $oldid)
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_SAME_VERSIONS'));
			return $this->displayTask();
		}

		// If no initial page is given, compare to the current revision
		$oldid = $oldid ?: $this->page->get('version_id');

		$or = $this->page->versions()->whereEquals('version', $oldid)->row();
		$dr = $this->page->versions()->whereEquals('version', $diff)->row();

		// Diff the two versions
		$ota = explode("\n", $or->get('pagetext'));
		$nta = explode("\n", $dr->get('pagetext'));

		$formatter = new \TableDiffFormatter();
		$result = $formatter->format(new \Diff($ota, $nta));

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
			Lang::txt(strtoupper($this->_option . '_' . $this->_task)),
			$this->page->link() . '&' . ($this->sub ? 'action' : 'task') . '=' . $this->_task
		);

		// Output view
		$this->view
			->set('parents', $parents)
			->set('page', $this->page)
			->set('sub', $this->sub)
			->set('content', $result)
			->set('or', $or)
			->set('dr', $dr)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Delete a revision
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getString('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Incoming
		$id = Request::getInt('oldid', 0);

		if (!$id || !$this->page->access('delete'))
		{
			App::redirect(
				Route::url($this->page->link('history'))
			);
		}

		$revision = Version::oneOrFail($id);

		// Get a count of all approved revisions
		$total = $this->page->versions()
			->whereEquals('approved', 1)
			->count();

		if ($total <= 1)
		{
			// Can't delete - it's the only approved version!
			App::redirect(
				Route::url($this->page->link('history'))
			);
		}

		// Mark as deleted
		$revision->set('approved', 2);

		if (!$revision->save())
		{
			App::redirect(
				Route::url($this->page->link('history')),
				Lang::txt('COM_WIKI_ERROR_REMOVING_REVISION'),
				'error'
			);
		}

		// If we're deleting the current revision, set the current
		// revision number to the previous available revision
		$last = $this->page->versions()
			->whereEquals('approved', 1)
			->order('version', 'desc')
			->row();

		$this->page->set('version_id', $last->get('id'));
		$this->page->save();
		$this->page->log('revision_removed');

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
				'action'      => 'deleted',
				'scope'       => 'wiki.page.revision',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_REVISION_DELETED', $revision->get('id'), '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
				'details'     => array(
					'title'    => $this->page->title,
					'url'      => Route::url($this->page->link()),
					'name'     => $this->page->get('pagename'),
					'revision' => $revision->get('id')
				)
			],
			'recipients' => $recipients
		]);

		App::redirect(
			Route::url($this->page->link('history'))
		);
	}

	/**
	 * Approve a revision
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getString('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Incoming
		$id = Request::getInt('oldid', 0);

		if (!$id || !$this->page->access('manage'))
		{
			App::redirect(
				Route::url($this->page->link())
			);
		}

		// Load the revision, approve it, and save
		$revision = Version::oneOrFail($id);
		$revision->set('approved', 1);
		if (!$revision->save())
		{
			App::abort(500, $revision->getError());
		}

		// Get the most recent revision and compare to the set "current" version
		$last = $this->page->versions()
			->whereEquals('approved', 1)
			->order('version', 'desc')
			->row();

		if ($last->get('id') == $revision->get('id'))
		{
			// The newly approved revision is now the most current
			// So, we need to update the page's version_id
			$this->page->set('version_id', $last->get('id'));
			$this->page->save();
		}

		$this->page->log('revision_approved');

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
				'action'      => 'approved',
				'scope'       => 'wiki.page.revision',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_REVISION_APPROVED', $revision->get('id'), '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
				'details'     => array(
					'title'    => $this->page->title,
					'url'      => Route::url($this->page->link()),
					'name'     => $this->page->get('pagename'),
					'revision' => $revision->get('id')
				)
			],
			'recipients' => $recipients
		]);

		App::redirect(
			Route::url($this->page->link())
		);
	}

	/**
	 * Set the current revision of a wiki page
	 * 
	 * @return void
	 */
	public function setcurrentrevisionTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			$url = Request::getString('REQUEST_URI', '', 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url), false)
			);
		}

		// Incoming
		$newversion_id = trim(Request::getString('version_id', ''));
		$newversion = Version::oneOrFail($newversion_id);
		if ($this->page->get('id') != $newversion->get('page_id'))
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'));
			return $this->displayTask();

		}

		$this->page->set('version_id', $newversion_id);

		if (!$this->page->save())
		{
			$this->setError($this->page->getError());
			return $this->displayTask();
		}
		$this->page->log('page_version_changed');

		Event::trigger('wiki.onWikiAfterSave', array(&$this->page, false));

		// Log activity
		$recipients = array(
			['wiki.site', 1],
			['user', $this->page->get('created_by')],
			['user', $this->page->version->get('created_by')],
			['user', $newversion->get('created_by')]
		);
		if ($this->page->get('scope') != 'site')
		{
			$recipients[]  = [$this->page->get('scope'), $this->page->get('scope_id')];
			$recipients[0] = ['wiki.' . $this->page->get('scope'), $this->page->get('scope_id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($this->page['id'] ? 'updated' : 'created'),
				'scope'       => 'wiki.page',
				'scope_id'    => $this->page->get('id'),
				'description' => Lang::txt('COM_WIKI_ACTIVITY_PAGE_UPDATED', '<a href="' . Route::url($this->page->link()) . '">' . $this->page->title . '</a>'),
				'details'     => array(
					'title'    => $this->page->title,
					'url'      => Route::url($this->page->link()),
					'name'     => $this->page->get('pagename'),
					'oldrevision' => $this->page->version->get('id'),
					'newrevision' => $this->page->get('version_id')
				)
			],
			'recipients' => $recipients
		]);
		App::redirect($this->page->link('history'));
	}


}
