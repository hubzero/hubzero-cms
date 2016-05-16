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

namespace Components\Wiki\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Version;
use Components\Wiki\Helpers\Parser;
use Request;
use User;
use Lang;
use Date;
use App;

/**
 * Controller class for wiki page revisions
 */
class Versions extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display all revisions for a page in the wiki(s)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'version'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			// Filters
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'pageid' => Request::getState(
				$this->_option . '.' . $this->_controller . '.pageid',
				'pageid',
				0,
				'int'
			)
		);

		$page = Page::oneOrNew(intval($filters['pageid']));

		// Get records
		$records = $page->versions();

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$records->whereLike('pagetext', $filters['search']);
		}

		$rows = $records
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('page', $page)
			->display();
	}

	/**
	 * Edit a revision
	 *
	 * @param   object  $row  Record
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$pageid = Request::getInt('pageid', 0);

		if (!$pageid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WIKI_ERROR_MISSING_ID'),
				'error'
			);
			return;
		}

		$page = Page::oneOrFail(intval($pageid));

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Version::oneOrNew($id);
		}

		if ($row->isNew())
		{
			// Creating new
			$current = $page->versions()
				->ordered()
				->row();

			$row->set('version', $current->get('version') + 1);
			$row->set('created_by', User::get('id'));
			$row->set('id', 0);
			$row->set('page_id', $page->get('id'));
		}

		// Output the HTML
		$this->view
			->set('page', $page)
			->set('row', $row)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a revision
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$revision = Request::getVar('revision', array(), 'post', 'none', 2);
		$revision = array_map('trim', $revision);

		// Initiate extended database class
		$version = Version::oneOrNew($revision['id']);

		// Get the "approved" state before binding incoming data
		$before = $version->get('approved');

		// Bind data
		$version->set($revision);

		// Get the parent page
		$page = Page::oneOrFail(intval($version->get('page_id')));

		// Parse text
		$parser = Parser::getInstance();
		$version->set('pagehtml', $parser->parse(
			$version->get('pagetext'),
			array(
				'option'   => $this->_option,
				'scope'    => $page->get('scope'),
				'scope_id' => $page->get('scope_id'),
				'path'     => $page->get('path'),
				'pagename' => $page->get('pagename'),
				'pageid'   => $page->get('id'),
				'filepath' => ''
			)
		));

		// Store new content
		if (!$version->save())
		{
			Notify::error($version->getError());
			return $this->editTask($version);
		}

		// Get the most recent revision and compare to the set "current" version
		if ($before != 1 && $version->get('approved') == 1)
		{
			$current = $page->versions()
				->whereEquals('approved', 1)
				->ordered()
				->row();

			if ($current->get('id') == $version->get('id'))
			{
				// The newly approved revision is now the most current
				// So, we need to update the page's version_id
				$page->set('version_id', $version->get('id'));
				$page->save();
			}

			$page->log('revision_approved');
		}

		// Set the success message
		Notify::success(Lang::txt('COM_WIKI_REVISION_SAVED'));

		// Fall through to the edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($version);
		}

		// Redirect to listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $version->get('page_id'), false)
		);
	}

	/**
	 * Delete a revision
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		$pageid = Request::getInt('pageid', 0);

		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			Notify::warning(Lang::txt('COM_WIKI_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		// Incoming
		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Output the HTML
				$this->view
					->set('ids', $ids)
					->set('pageid', $pageid)
					->setErrors($this->getErrors())
					->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken();

				// Check if they confirmed
				$confirmed = Request::getInt('confirm', 0);

				if (!$confirmed)
				{
					Notify::error(Lang::txt('COM_WIKI_CONFIRM_DELETE'));

					// Output the HTML
					$this->view
						->set('ids', $ids)
						->set('pageid', $pageid)
						->setErrors($this->getErrors())
						->display();
					return;
				}

				$i = 0;

				foreach ($ids as $id)
				{
					// Load the revision
					$version = Version::oneOrFail($id);

					$pageid = $version->get('page_id', $pageid);

					// Get a count of all approved revisions
					$count = Version::all()
						->whereEquals('pageid', $pageid)
						->whereEquals('approved', 1)
						->total();

					// Can't delete - it's the only approved version!
					if ($count <= 1)
					{
						Notify::error(Lang::txt('COM_WIKI_ERROR_CANNOT_REMOVE_REVISION'));
						continue;
					}

					// Delete it
					if (!$version->destroy())
					{
						Notify::error($version->getError());
					}

					$i++;
				}

				if ($i)
				{
					Notify::success(Lang::txt('COM_WIKI_PAGES_DELETED', $i));
				}

				// Set the redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . $pageid, false)
				);
			break;
		}
	}

	/**
	 * Set the approval state for a revision
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the revision, approve it, and save
			$version = Version::oneOrFail($id);
			$version->set('approved', Request::getInt('approve', 0));

			if (!$version->save())
			{
				Notify::error($version->getError());
			}
		}

		$this->cancelTask();
	}

	/**
	 * Cancel a task and redirect to main listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&pageid=' . Request::getInt('pageid', 0), false)
		);
	}
}
